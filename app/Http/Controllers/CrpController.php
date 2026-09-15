<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlan;
use App\Models\CapacityPlanning;
use App\Models\WorkCenter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CrpController extends Controller
{
    public function show($planId)
    {
        $plan = ProductionPlan::findOrFail($planId);
        $capacities = CapacityPlanning::with('work_center')
            ->where('production_plan_id', $planId)
            ->get();

        // Data untuk Chart.js
        $chartData = [
            'labels' => $capacities->pluck('work_center.name'),
            'load' => $capacities->pluck('required_hours'),
            'capacity' => $capacities->pluck('available_hours'),
        ];

        return view('ppic.crp.show', compact('plan', 'capacities', 'chartData'));
    }

    public function generate($planId)
    {
        $plan = ProductionPlan::with(['items.product.routings'])->findOrFail($planId);

        // === VALIDASI TAMBAHAN (REVISI: RINGKAS & RAPI) ===
        $productsInMps = $plan->items->pluck('product_id')->unique();

        $productsWithRouting = \App\Models\ProductRouting::whereIn('product_id', $productsInMps)
            ->pluck('product_id')
            ->unique();

        $missingRoutingIds = $productsInMps->diff($productsWithRouting);

        if ($missingRoutingIds->isNotEmpty()) {
            $totalMissing = $missingRoutingIds->count();

            // Ambil 5 nama produk pertama sebagai contoh
            $exampleProducts = \App\Models\Product::whereIn('id', $missingRoutingIds->take(5))
                ->pluck('name')
                ->implode(', ');

            // Buat pesan error yang rapi (Support HTML)
            $errorMessage = "<strong>Gagal menghitung!</strong><br>";
            $errorMessage .= "Terdapat <span class='text-rose-600 font-bold'>{$totalMissing} produk</span> yang belum memiliki data Routing (Waktu Baku).<br><br>";
            $errorMessage .= "Contoh produk:<br><span class='text-sm text-slate-500'>{$exampleProducts}...</span><br><br>";
            $errorMessage .= "Harap lengkapi data Routing di Master Data terlebih dahulu.";

            return redirect()->back()->with('error', $errorMessage);
        }

        // Cek apakah MPS kosong
        if ($plan->items->sum('production_qty') <= 0) {
            return redirect()->back()->with('error', "Gagal menghitung! Rencana produksi pada MPS masih kosong (0).");
        }
        // ===================================================

        // ... (Kode kalkulasi standar kapasitas di bawah tetap sama) ...
        $workingDays = 22;
        $hoursPerDay = 8;
        $standardCapacity = $workingDays * $hoursPerDay;

        DB::transaction(function () use ($plan, $standardCapacity) {
            // ... (Logika simpan ke DB tetap sama, jangan diubah) ...
            // Hapus hitungan lama
            CapacityPlanning::where('production_plan_id', $plan->id)->delete();

            $loadPerWC = [];

            // Loop setiap Item di MPS
            foreach ($plan->items as $mpsItem) {
                $qty = $mpsItem->production_qty;

                if ($qty > 0 && $mpsItem->product->routings->isNotEmpty()) {
                    foreach ($mpsItem->product->routings as $route) {
                        $wcId = $route->work_center_id;
                        $stdTime = $route->standard_time;

                        // Konversi waktu ke JAM
                        $hoursNeeded = 0;
                        switch ($route->time_unit) {
                            case 'seconds':
                                $hoursNeeded = ($stdTime * $qty) / 3600;
                                break;
                            case 'minutes':
                                $hoursNeeded = ($stdTime * $qty) / 60;
                                break;
                            case 'hours':
                                $hoursNeeded = ($stdTime * $qty);
                                break;
                            default: // Default assume minutes if null
                                $hoursNeeded = ($stdTime * $qty) / 60;
                        }

                        if (!isset($loadPerWC[$wcId])) {
                            $loadPerWC[$wcId] = 0;
                        }
                        $loadPerWC[$wcId] += $hoursNeeded;
                    }
                }
            }

            // Simpan Analisis ke DB
            $allWC = WorkCenter::all();

            foreach ($allWC as $wc) {
                $required = $loadPerWC[$wc->id] ?? 0;
                $available = $standardCapacity;

                $utilization = $available > 0 ? ($required / $available) * 100 : 0;

                // Tentukan Status
                $status = 'optimal';
                if ($utilization > 100) $status = 'overload';
                elseif ($utilization < 50) $status = 'underload';

                CapacityPlanning::create([
                    'production_plan_id' => $plan->id,
                    'work_center_id' => $wc->id,
                    'required_hours' => $required,
                    'available_hours' => $available,
                    'utilization_percentage' => $utilization,
                    'status' => $status
                ]);
            }
        });

        return redirect()->back()->with('success', 'Analisis CRP Selesai.');
    }
}
