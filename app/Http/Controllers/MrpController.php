<?php

namespace App\Http\Controllers;

use App\Models\ProductionPlan;
use App\Models\RawMaterialRequirement;
use App\Models\BillOfMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MrpController extends Controller
{
    public function show($id)
    {
        $plan = ProductionPlan::findOrFail($id);
        $requirements = RawMaterialRequirement::with('raw_material')
            ->where('production_plan_id', $id)
            ->get();

        return view('ppic.mrp.show', compact('plan', 'requirements'));
    }

    public function generate($id)
    {
        $plan = ProductionPlan::with('items')->findOrFail($id);

        // === VALIDASI BARU: CEK APAKAH MPS MASIH KOSONG ===
        $totalInput = $plan->items->sum(function ($item) {
            return $item->sales_forecast + $item->production_qty;
        });

        if ($totalInput <= 0) {
            return back()->with('error', 'Gagal menghitung! Data Forecast dan Rencana Produksi di MPS masih kosong (0). Silakan isi data MPS terlebih dahulu.');
        }
        // ==================================================

        DB::transaction(function () use ($plan) {
            // 1. Hapus perhitungan lama agar tidak duplikat
            RawMaterialRequirement::where('production_plan_id', $plan->id)->delete();

            // 2. Loop setiap item di MPS
            foreach ($plan->items as $mpsItem) {
                // Ambil BOM untuk produk ini
                $boms = BillOfMaterial::where('product_id', $mpsItem->product_id)->get();

                foreach ($boms as $bom) {
                    // Hitung Kebutuhan Kotor: Rencana Produksi x Kebutuhan Material per Unit
                    $grossReq = $mpsItem->production_qty * $bom->quantity;

                    // Cek apakah material ini sudah ada di list requirement plan ini? (Gabungkan jika ada produk beda pakai material sama)
                    $requirement = RawMaterialRequirement::firstOrNew([
                        'production_plan_id' => $plan->id,
                        'raw_material_id' => $bom->raw_material_id
                    ]);

                    // Tambahkan ke requirement yang ada
                    $requirement->gross_requirement = ($requirement->gross_requirement ?? 0) + $grossReq;

                    // Ambil stok gudang material saat ini (Stok Realtime)
                    // Asumsi: Anda punya relasi stok di model RawMaterial
                    $requirement->current_stock = $bom->raw_material->stock ?? 0;

                    // Hitung Net Requirement (Yang harus dibeli)
                    // Rumus: Gross - Stok. Jika minus (stok sisa), set 0.
                    $net = $requirement->gross_requirement - $requirement->current_stock;
                    $requirement->net_requirement = $net > 0 ? $net : 0;

                    $requirement->unit = $bom->unit;
                    $requirement->save();
                }
            }
        });

        return back()->with('success', 'Perhitungan MRP berhasil diselesaikan.');
    }
}
