<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Machine;
use App\Models\PackagingType;
use App\Models\DailyReport;
use Illuminate\Http\Request;
use App\Exports\ProductsExport;
use App\Exports\ProductTemplateExport;
use App\Imports\ProductsImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['machines', 'packagingType'])->withCount('routings');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $products = $query->orderBy('id', 'desc')->paginate(10);

        // --- LOGIKA UTAMA: INJECT DATA ACTUAL DARI DAILY REPORT ---
        foreach ($products as $product) {
            foreach ($product->machines as $machine) {
                $latestReport = DailyReport::where('product_id', $product->id)
                    ->where('machine_id', $machine->id)
                    ->orderBy('production_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($latestReport) {
                    $machine->pivot->actual_cavity = $latestReport->actual_cavity;
                    $machine->pivot->actual_cycle_time = $latestReport->actual_cycle_time;
                } else {
                    $machine->pivot->actual_cavity = 0;
                    $machine->pivot->actual_cycle_time = 0;
                }
            }
        }
        // ---------------------------------------------------------

        $machines = Machine::where('status', 'active')->get();
        $packagingTypes = PackagingType::where('status', 'active')->orderBy('name')->get();

        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'aktif')->count();
        $inactiveProducts = Product::where('status', 'nonaktif')->count();

        $products->appends($request->all());

        return view('products.index', compact(
            'products',
            'machines',
            'packagingTypes',
            'totalProducts',
            'activeProducts',
            'inactiveProducts'
        ));
    }

    public function show($id)
    {
        $product = Product::with(['machines', 'packagingType'])->withCount('routings')->findOrFail($id);

        // --- LOGIKA UTAMA: INJECT DATA ACTUAL UNTUK MODAL ---
        foreach ($product->machines as $machine) {
            $latestReport = DailyReport::where('product_id', $product->id)
                ->where('machine_id', $machine->id)
                ->orderBy('production_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->first();

            if ($latestReport) {
                $machine->pivot->actual_cavity = $latestReport->actual_cavity;
                $machine->pivot->actual_cycle_time = $latestReport->actual_cycle_time;
            } else {
                $machine->pivot->actual_cavity = 0;
                $machine->pivot->actual_cycle_time = 0;
            }
        }
        // ----------------------------------------------------

        return response()->json($product);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'packaging_type_id' => 'nullable|exists:packaging_types,id',
            'machine_id' => 'required|array', // Harus berupa array
            'machine_id.*' => 'exists:machines,id',
            'cycle_time' => 'nullable|array',
            'cavity' => 'nullable|array',
        ]);

        $weight = 0;
        if ($request->filled('weight')) {
            $weight = str_replace(',', '.', $request->weight);
        }

        $product = Product::create([
            'name' => $request->name,
            'status' => $request->status,
            'packaging_type_id' => $request->packaging_type_id,
            'weight' => $weight
        ]);

        // Menyusun array sinkronisasi untuk multi-mesin
        $machineData = [];
        if ($request->has('machine_id')) {
            foreach ($request->machine_id as $index => $machineId) {
                if (!$machineId) continue;

                $ct = !empty($request->cycle_time[$index]) ? str_replace(',', '.', $request->cycle_time[$index]) : 0;
                $cav = !empty($request->cavity[$index]) ? $request->cavity[$index] : 1;

                $machineData[$machineId] = [
                    'cycle_time' => (float) $ct,
                    'cavity' => (int) $cav,
                    'actual_cycle_time' => 0,
                    'actual_cavity' => 0
                ];
            }
        }

        // Simpan semua mesin beserta nilainya masing-masing
        $product->machines()->attach($machineData);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'status' => 'required',
            'packaging_type_id' => 'nullable|exists:packaging_types,id',
            'machine_id' => 'required|array', // Harus berupa array
            'machine_id.*' => 'exists:machines,id',
            'cycle_time' => 'nullable|array',
            'cavity' => 'nullable|array',
        ]);

        $weight = 0;
        if ($request->filled('weight')) {
            $weight = str_replace(',', '.', $request->weight);
        }

        $product = Product::findOrFail($id);
        $product->update([
            'name' => $request->name,
            'status' => $request->status,
            'packaging_type_id' => $request->packaging_type_id,
            'weight' => $weight
        ]);

        // Ambil data pivot yang lama agar tidak menimpa data "Actual" yang berasal dari Laporan Harian
        $existingPivots = $product->machines()->get()->keyBy('id');

        $machineData = [];
        if ($request->has('machine_id')) {
            foreach ($request->machine_id as $index => $machineId) {
                if (!$machineId) continue;

                $ct = !empty($request->cycle_time[$index]) ? str_replace(',', '.', $request->cycle_time[$index]) : 0;
                $cav = !empty($request->cavity[$index]) ? $request->cavity[$index] : 1;

                $actCt = 0;
                $actCav = 0;

                // Jika mesin sudah pernah diset, pertahankan data aktualnya
                if ($existingPivots->has($machineId)) {
                    $actCt = $existingPivots[$machineId]->pivot->actual_cycle_time;
                    $actCav = $existingPivots[$machineId]->pivot->actual_cavity;
                }

                $machineData[$machineId] = [
                    'cycle_time' => (float) $ct,
                    'cavity' => (int) $cav,
                    'actual_cycle_time' => $actCt,
                    'actual_cavity' => $actCav
                ];
            }
        }

        // Sync akan mengganti relasi lama dengan relasi array baru secara utuh
        $product->machines()->sync($machineData);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diupdate');
    }

    public function destroy($id)
    {
        try {
            $product = Product::findOrFail($id);
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
        } catch (QueryException $e) {
            if ($e->errorInfo == 1451) {
                return redirect()->back()->with('error', 'Gagal menghapus! Produk sedang digunakan.');
            }
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $product = Product::findOrFail($id);
            $newStatus = ($product->status == 'aktif') ? 'nonaktif' : 'aktif';
            $product->status = $newStatus;
            $product->save();
            return response()->json(['success' => true, 'new_status' => $newStatus, 'message' => 'Status berhasil diubah']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-produk-' . date('d-m-Y');
            if ($format === 'pdf') {
                $products = Product::with(['machines', 'packagingType'])->get();
                $pdf = Pdf::loadView('exports.products', ['products' => $products])->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new ProductsExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new ProductsImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new ProductTemplateExport, 'template_produk.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
