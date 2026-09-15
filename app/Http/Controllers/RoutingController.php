<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\WorkCenter;
use App\Models\ProductRouting;
use Illuminate\Http\Request;

class RoutingController extends Controller
{
    public function index(Request $request)
    {
        // List Produk
        $products = Product::withCount('routings')->orderBy('name')->get();
        // Master Work Center
        $workCenters = WorkCenter::orderBy('name')->get();

        return view('ppic.routing.index', compact('products', 'workCenters'));
    }

    public function show($id)
    {
        // Edit Routing Produk
        $product = Product::with(['routings.work_center'])->findOrFail($id);
        $workCenters = WorkCenter::orderBy('name')->get();

        return view('ppic.routing.show', compact('product', 'workCenters'));
    }

    public function storeWorkCenter(Request $request)
    {
        $request->validate([
            'wc_code' => 'required|unique:work_centers',
            'name' => 'required'
        ]);

        WorkCenter::create($request->all());
        return redirect()->back()->with('success', 'Work Center berhasil dibuat.');
    }

    public function storeStep(Request $request, $product_id)
    {
        $request->validate([
            'step_number' => 'required|numeric',
            'work_center_id' => 'required',
            'operation_name' => 'required',
            'standard_time' => 'required|numeric'
        ]);

        ProductRouting::create([
            'product_id' => $product_id,
            'step_number' => $request->step_number,
            'work_center_id' => $request->work_center_id,
            'operation_name' => $request->operation_name,
            'standard_time' => $request->standard_time,
            'time_unit' => $request->time_unit
        ]);

        return redirect()->back()->with('success', 'Proses ditambahkan.');
    }

    public function destroyStep($id)
    {
        ProductRouting::destroy($id);
        return redirect()->back()->with('success', 'Proses dihapus.');
    }
}
