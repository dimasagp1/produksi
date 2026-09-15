<?php

namespace App\Http\Controllers;

use App\Models\PackagingType;
use App\Models\Product; // Import Model Product
use Illuminate\Http\Request;
use App\Exports\PackagingTypesExport;
use App\Exports\PackagingTypeTemplateExport;
use App\Imports\PackagingTypesImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PackagingTypeController extends Controller
{
    public function index(Request $request)
    {
        // PERUBAHAN DISINI: Tambahkan with('products')
        // Agar data detail produk bisa diambil di view index
        $query = PackagingType::with('products')->withCount('products');

        if ($request->filled('search')) {
            $query->where('name', 'LIKE', "%{$request->search}%");
        }

        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        }

        $packagingTypes = $query->orderBy('name')->paginate(15);

        // Ambil data produk untuk dropdown modal
        $products = Product::where('status', 'aktif')->orderBy('name')->get();

        $packagingTypes->appends($request->all());

        return view('packaging-types.index', compact('packagingTypes', 'products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:packaging_types,name',
            'status' => 'required',
            'content_unit' => 'nullable|string',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        $packagingType = PackagingType::create($request->except('products'));

        if ($request->has('products')) {
            Product::whereIn('id', $request->products)->update(['packaging_type_id' => $packagingType->id]);
        }

        return redirect()->route('packaging-types.index')
            ->with('success', 'Jenis Kemasan berhasil ditambahkan');
    }

    public function update(Request $request, PackagingType $packagingType)
    {
        $request->validate([
            'name' => 'required|unique:packaging_types,name,' . $packagingType->id,
            'status' => 'required',
            'content_unit' => 'nullable|string',
            'product_qty' => 'nullable|array',
            'product_qty.*' => 'nullable|numeric|min:0',
        ]);

        // 1. Update Data Utama Kemasan
        $packagingType->update($request->only(['name', 'status', 'content_unit']));

        // 2. LOGIKA BARU: SYNC PRODUK (Tambah yang dipilih, Hapus yang tidak dipilih)
        // Ambil ID produk yang dipilih dari form (jika tidak ada, anggap array kosong)
        $selectedProductIds = $request->input('products', []);

        // A. Update produk yang DIPILIH -> Set ID Kemasan ini
        if (!empty($selectedProductIds)) {
            Product::whereIn('id', $selectedProductIds)
                ->update(['packaging_type_id' => $packagingType->id]);
        }

        // B. Update produk yang TIDAK DIPILIH (tapi dulunya pakai kemasan ini) -> Set NULL
        // Ini yang sebelumnya kurang, sehingga "Botol Contoh A" tetap nyangkut
        Product::where('packaging_type_id', $packagingType->id)
            ->whereNotIn('id', $selectedProductIds)
            ->update([
                'packaging_type_id' => null,
                'packaging_qty' => null // Reset qty khusus juga biar bersih
            ]);

        // 3. Update Qty Spesifik (Jika ada inputan angka)
        if ($request->has('product_qty')) {
            foreach ($request->product_qty as $productId => $qty) {
                // Hanya update jika produknya memang termasuk yang dipilih
                if (in_array($productId, $selectedProductIds)) {
                    $val = ($qty > 0) ? $qty : null;
                    Product::where('id', $productId)->update(['packaging_qty' => $val]);
                }
            }
        }

        return redirect()->route('packaging-types.index')->with('success', 'Data berhasil diupdate');
    }

    public function destroy(PackagingType $packagingType)
    {
        try {
            $packagingType->products()->update(['packaging_type_id' => null]);
            $packagingType->delete();
            return redirect()->route('packaging-types.index')->with('success', 'Jenis Kemasan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        $packagingType = PackagingType::findOrFail($id);
        $newStatus = ($packagingType->status == 'active') ? 'inactive' : 'active';
        $packagingType->status = $newStatus;
        $packagingType->save();
        return response()->json(['success' => true, 'new_status' => $newStatus]);
    }

    // --- IMPORT / EXPORT (Tetap Sama) ---
    public function export($format)
    {
        try {
            if (ob_get_length()) ob_end_clean();
            $fileName = 'data-kemasan-' . date('d-m-Y');
            if ($format === 'pdf') {
                $packagingTypes = PackagingType::with('products')->get();
                $pdf = Pdf::loadView('exports.packaging_types', ['packagingTypes' => $packagingTypes]);
                $pdf->setPaper('A4', 'portrait');
                return $pdf->download($fileName . '.pdf');
            } else {
                return Excel::download(new PackagingTypesExport, $fileName . '.xlsx');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal export data: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);
        try {
            Excel::import(new PackagingTypesImport, $request->file('file'));
            return redirect()->back()->with('success', 'Import Sukses!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import Gagal: ' . $e->getMessage());
        }
    }

    public function template()
    {
        try {
            if (ob_get_length()) ob_end_clean();
            return Excel::download(new PackagingTypeTemplateExport, 'template_kemasan.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal download template: ' . $e->getMessage());
        }
    }
}
