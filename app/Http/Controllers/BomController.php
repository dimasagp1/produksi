<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\BillOfMaterial;
use Illuminate\Http\Request;

class BomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        // 1. Query Produk dengan Search & Pagination
        $products = Product::withCount('bom_items')
            ->when($search, function ($query) use ($search) {
                // Cari berdasarkan nama
                $query->where('name', 'like', "%{$search}%");

                // [PERBAIKAN] Saya comment baris ini karena menyebabkan error (Kolom 'code' tidak ditemukan)
                // Jika tabel products Anda menggunakan 'sku' atau 'product_code', ubah 'code' di bawah ini:
                // ->orWhere('sku', 'like', "%{$search}%"); 
            })
            ->orderBy('name')
            ->paginate(12, ['*'], 'products_page')
            ->withQueryString();

        // 2. Query Material dengan Search & Pagination
        $materials = RawMaterial::orderBy('name')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('material_code', 'like', "%{$search}%");
            })
            ->when($request->has('low_stock'), function ($query) {
                $query->where('stock', '<', 10);
            })
            ->paginate(12, ['*'], 'materials_page')
            ->withQueryString();

        // 3. Stats
        $totalProductsCount = Product::count();
        $totalMaterialsCount = RawMaterial::count();
        $lowStockCount = RawMaterial::where('stock', '<', 10)->count();

        return view('ppic.bom.index', compact(
            'products',
            'materials',
            'totalProductsCount',
            'totalMaterialsCount',
            'lowStockCount',
            'search'
        ));
    }

    public function show($id)
    {
        $product = Product::with('bom_items.raw_material')->findOrFail($id);
        $materials = RawMaterial::orderBy('name')->get();

        $totalCost = 0;
        foreach ($product->bom_items as $item) {
            $totalCost += $item->quantity * $item->raw_material->std_cost;
        }

        return view('ppic.bom.show', compact('product', 'materials', 'totalCost'));
    }

    public function storeMaterial(Request $request)
    {
        $request->validate([
            'material_code' => 'required|unique:raw_materials',
            'name' => 'required',
            'unit' => 'required'
        ]);

        RawMaterial::create($request->all());
        return redirect()->back()->with('success', 'Material baru ditambahkan.');
    }

    public function storeItem(Request $request, $product_id)
    {
        $request->validate([
            'raw_material_id' => 'required',
            'quantity' => 'required|numeric|min:0.0001',
        ]);

        $exists = BillOfMaterial::where('product_id', $product_id)
            ->where('raw_material_id', $request->raw_material_id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Material ini sudah ada di BOM.');
        }

        $material = RawMaterial::find($request->raw_material_id);

        BillOfMaterial::create([
            'product_id' => $product_id,
            'raw_material_id' => $request->raw_material_id,
            'quantity' => $request->quantity,
            'unit' => $material->unit
        ]);

        return redirect()->back()->with('success', 'Material ditambahkan ke BOM.');
    }

    public function destroyItem($id)
    {
        BillOfMaterial::destroy($id);
        return redirect()->back()->with('success', 'Item dihapus dari BOM.');
    }
}
