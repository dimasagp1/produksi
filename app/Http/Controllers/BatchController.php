<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Product;
use App\Models\Color;
use App\Models\Machine;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Exports\BatchExport;
use App\Exports\BatchTemplateExport;
use App\Imports\BatchImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\QueryException;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Batch::with(['product', 'color', 'machine', 'reports']);

        // 1. FILTER SEARCH (DIPERBAIKI)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $term = '%' . $search . '%';
                $q->where('batch_code', 'LIKE', $term)
                    ->orWhereHas('product', fn($sq) => $sq->where('name', 'LIKE', $term))
                    ->orWhereHas('color', fn($sq) => $sq->where('name', 'LIKE', $term))     // Tambahan: Cari Warna
                    ->orWhereHas('machine', fn($sq) => $sq->where('name', 'LIKE', $term));   // Tambahan: Cari Mesin
            });
        }

        // 2. FILTER STATUS SPESIFIK
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // 3. FILTER STATUS GROUP (AKTIF / NON-AKTIF)
        if ($request->filled('status_group')) {
            if ($request->status_group == 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status_group == 'nonactive') {
                $query->where('is_active', 0);
            }
        }

        // 4. FILTER TANGGAL
        if ($request->filled('filter_start_date') && $request->filled('filter_end_date')) {
            try {
                // Validasi format tanggal sederhana agar tidak error query
                $start = \Carbon\Carbon::parse($request->filter_start_date)->startOfDay();
                $end = \Carbon\Carbon::parse($request->filter_end_date)->endOfDay();

                $query->whereBetween('start_date', [$start, $end]);
            } catch (\Exception $e) {
                // Abaikan jika format tanggal salah (user ketik manual ngawur)
            }
        }

        // 5. SORTING: TANGGAL BARU PALING ATAS
        $batches = $query->orderBy('created_at', 'desc')->paginate(12);

        // Penting: Appends agar filter tidak hilang saat pindah halaman
        $batches->appends($request->all());

        // --- STATISTIK ---
        $stats = [
            'total' => Batch::count(),
            'active' => Batch::where('is_active', 1)->count(),
            'non_active' => Batch::where('is_active', 0)->count(),
            'running' => Batch::where('status', 'running')->count(),
            'planning' => Batch::where('status', 'planning')->count(),
            'completed' => Batch::where('status', 'completed')->count(),
        ];

        $products = Product::where('status', 'aktif')->get();
        $colors = Color::where('status', 'active')->get();
        $machines = Machine::where('status', 'active')->get();

        return view('ppic.batches.index', compact('batches', 'products', 'colors', 'machines', 'stats'));
    }

    public function store(Request $request)
    {
        if ($request->has('target_quantity')) {
            $request->merge(['target_quantity' => str_replace('.', '', $request->target_quantity)]);
        }

        $request->validate([
            'product_id' => 'required',
            'color_id' => 'required',
            'machine_id' => 'required',
            'target_quantity' => 'required|numeric|min:1',
            'start_date' => 'required|date',
            'deadline_date' => 'required|date|after_or_equal:start_date',
            'priority' => 'required',
        ]);

        $data = $request->all();
        $data['is_active'] = 1; // Default Active

        if (empty($data['batch_code'])) {
            $today = date('dmy');
            $latestBatch = Batch::where('batch_code', 'like', 'EKDEV-' . $today . '%')->count();
            $number = str_pad($latestBatch + 1, 3, '0', STR_PAD_LEFT);
            $data['batch_code'] = 'EKDEV-' . $today . '-' . $number;
        }

        Batch::create($data);
        return redirect()->route('batches.index')->with('success', 'Batch berhasil dibuat!');
    }

    public function update(Request $request, Batch $batch)
    {
        // [FITUR LOCK] Cek apakah status sudah final (completed/canceled)
        if (in_array($batch->status, ['completed', 'canceled'])) {
            return redirect()->route('batches.index')
                ->with('error', 'Gagal update! Batch ini terkunci (status Completed/Canceled). Silakan Unlock terlebih dahulu.');
        }

        if ($request->has('target_quantity')) {
            $request->merge(['target_quantity' => str_replace('.', '', $request->target_quantity)]);
        }

        $request->validate([
            'target_quantity' => 'required|numeric',
            'priority' => 'required',
            'machine_id' => 'required',
            'start_date' => 'nullable|date',
            'deadline_date' => 'required|date|after_or_equal:start_date',
        ]);

        // Update data batch
        $batch->update($request->all());

        // Cek jika Target Quantity baru <= Current Quantity
        if ($batch->current_quantity >= $batch->target_quantity && $batch->status == 'running') {
            $batch->update(['status' => 'completed']);

            return redirect()->route('batches.index')
                ->with('success', 'Batch berhasil diupdate dan OTOMATIS TERKUNCI (Completed) karena target terpenuhi.');
        }
        // ------------------------------------

        return redirect()->route('batches.index')->with('success', 'Batch berhasil diupdate');
    }

    public function destroy(Batch $batch)
    {
        // [FITUR LOCK] Cek apakah status sudah final
        if (in_array($batch->status, ['completed', 'canceled'])) {
            return redirect()->route('batches.index')
                ->with('error', 'Gagal menghapus! Batch ini terkunci (status Completed/Canceled).');
        }

        try {
            $batch->delete();
            return redirect()->route('batches.index')->with('success', 'Batch berhasil dihapus');
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1451) {
                return redirect()->route('batches.index')
                    ->with('error', 'Gagal menghapus! Batch ini memiliki data Daily Report.');
            }
            return redirect()->route('batches.index')
                ->with('error', 'Terjadi kesalahan database.');
        }
    }

    public function toggleActive(Request $request, Batch $batch)
    {
        $batch->is_active = !$batch->is_active;
        $batch->save();

        return response()->json([
            'success' => true,
            'message' => 'Status batch berhasil diubah!',
            'is_active' => $batch->is_active
        ]);
    }

    public function toggleLock(Request $request, Batch $batch)
    {
        // Cek apakah saat ini terkunci (status completed/canceled)
        $isLocked = in_array($batch->status, ['completed', 'canceled']);

        if ($isLocked) {
            $batch->status = 'running';
            $message = 'Batch dibuka (Unlock)! Status kembali ke Running.';
            $newLockedState = false;
        } else {
            $batch->status = 'completed';
            $message = 'Batch dikunci (Lock)! Status diubah menjadi Completed.';
            $newLockedState = true;
        }

        $batch->save();

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_locked' => $newLockedState,
            'new_status' => $batch->status
        ]);
    }

    // --- EXPORT / IMPORT ---

    public function exportExcel(Request $request)
    {
        return Excel::download(new BatchExport($request), 'Data_Batch_Produksi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $query = Batch::with(['product', 'color', 'machine']);

        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('status_group')) {
            if ($request->status_group == 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status_group == 'nonactive') {
                $query->where('is_active', 0);
            }
        }
        if ($request->filled('filter_start_date') && $request->filled('filter_end_date')) {
            $query->whereBetween('start_date', [$request->filter_start_date, $request->filter_end_date]);
        }

        $batches = $query->orderBy('start_date', 'desc')->get();

        $pdf = Pdf::loadView('exports.batches', compact('batches'))->setPaper('a4', 'landscape');
        return $pdf->download('Data_Batch_Produksi.pdf');
    }

    public function downloadTemplate()
    {
        return Excel::download(new BatchTemplateExport, 'template_import_batch.xlsx');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls']);
        try {
            Excel::import(new BatchImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Batch berhasil diimport!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }
}
