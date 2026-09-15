<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlan;
use App\Models\ProductionPlanItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MpsController extends Controller
{
    public function index(Request $request)
    {
        // Query Dasar
        $query = ProductionPlan::orderBy('period', 'desc');

        // Filter Berdasarkan Status (Summary Card Click)
        if ($request->has('status')) {
            $status = $request->status;
            if (in_array($status, ['draft', 'confirmed', 'closed'])) {
                $query->where('status', $status);
            }
        }

        // Data MPS dengan Pagination
        $plans = $query->paginate(9)->withQueryString();

        // --- DATA UNTUK SUMMARY CARD (REALTIME) ---
        $totalPlans     = ProductionPlan::count();
        $draftPlans     = ProductionPlan::where('status', 'draft')->count();
        $confirmedPlans = ProductionPlan::where('status', 'confirmed')->count();
        $closedPlans    = ProductionPlan::where('status', 'closed')->count();
        // ------------------------------------------

        return view('ppic.mps.index', compact('plans', 'totalPlans', 'draftPlans', 'confirmedPlans', 'closedPlans'));
    }

    public function create()
    {
        return view('ppic.mps.create');
    }

    public function store(Request $request)
    {
        $request->validate(['period' => 'required|date']);

        $date = Carbon::parse($request->period);
        $code = 'MPS-' . $date->format('Y-m');

        if (ProductionPlan::where('plan_code', $code)->exists()) {
            return back()->with('error', 'MPS untuk periode ini sudah ada.');
        }

        DB::transaction(function () use ($date, $code) {
            $plan = ProductionPlan::create([
                'plan_code' => $code,
                'period' => $date->startOfMonth(),
                'status' => 'draft'
            ]);

            $products = Product::all();

            foreach ($products as $product) {
                $currentStock = $product->stock ?? 0;

                ProductionPlanItem::create([
                    'production_plan_id' => $plan->id,
                    'product_id' => $product->id,
                    'beginning_stock' => $currentStock,
                    'sales_forecast' => 0,
                    'production_qty' => 0,
                    'ending_stock' => $currentStock
                ]);
            }
        });

        return redirect()->route('mps.index')->with('success', 'Draft MPS berhasil dibuat.');
    }

    public function show($id)
    {
        $plan = ProductionPlan::with(['items.product'])->findOrFail($id);
        return view('ppic.mps.show', compact('plan'));
    }

    public function updateItems(Request $request, $id)
    {
        $plan = ProductionPlan::findOrFail($id);

        if ($plan->status == 'closed') {
            return back()->with('error', 'MPS sudah ditutup, tidak bisa diedit.');
        }

        foreach ($request->items as $itemId => $data) {
            $item = ProductionPlanItem::findOrFail($itemId);

            $forecast = (int) $data['sales_forecast'];
            $production = (int) $data['production_qty'];
            $ending = $item->beginning_stock + $production - $forecast;

            $item->update([
                'sales_forecast' => $forecast,
                'production_qty' => $production,
                'ending_stock' => $ending
            ]);
        }

        if ($request->has('confirm_plan')) {
            $plan->update(['status' => 'confirmed']);
        }

        return back()->with('success', 'Data MPS diperbarui.');
    }

    // --- FITUR HAPUS PERIODE ---
    public function destroy($id)
    {
        $plan = ProductionPlan::findOrFail($id);

        // PERBAIKAN: Hanya status 'closed' yang DILARANG dihapus.
        // Draft dan Confirmed BOLEH dihapus.
        if ($plan->status === 'closed') {
            return back()->with('error', 'Periode MPS yang sudah CLOSED tidak dapat dihapus karena sudah menjadi arsip.');
        }

        DB::transaction(function () use ($plan) {
            $plan->items()->delete(); // Hapus detail item
            $plan->delete();          // Hapus header plan
        });

        return redirect()->route('mps.index')->with('success', 'Periode MPS berhasil dihapus.');
    }
}
