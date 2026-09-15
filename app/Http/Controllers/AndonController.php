<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AndonController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ambil semua mesin aktif
        $machines = Machine::where('status', 'active')->orderBy('name')->get();

        $andonData = $machines->map(function ($machine) use ($today) {
            // Ambil laporan aktif hari ini
            $report = DailyReport::with(['product', 'batch'])
                ->where('machine_id', $machine->id)
                ->whereDate('production_date', $today)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$report) {
                // STATUS: IDLE (OFFLINE)
                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'status' => 'idle',
                    'color_class' => 'bg-slate-800 border-slate-600 text-slate-400', // Gelap/Abu
                    'product' => '-',
                    'target' => 0,
                    'actual' => 0,
                    'efficiency' => 0,
                    'downtime' => 0,
                    'message' => 'MESIN OFF'
                ];
            }

            // Jika status verified, berarti sudah selesai shift/batch ini
            if ($report->status === 'verified') {
                return [
                    'id' => $machine->id,
                    'name' => $machine->name,
                    'status' => 'completed',
                    'color_class' => 'bg-blue-900 border-blue-700 text-blue-100', // Biru
                    'product' => $report->product->name,
                    'target' => $report->qty_theory,
                    'actual' => $report->total_output,
                    'efficiency' => $report->efficiency,
                    'downtime' => $report->downtime_total,
                    'message' => 'SELESAI'
                ];
            }

            // STATUS: RUNNING (Aktif)
            // Tentukan warna berdasarkan Efisiensi & Downtime
            $eff = $report->efficiency;
            $dt = $report->downtime_total;

            $colorClass = 'bg-emerald-600 border-emerald-500 text-white'; // Default: Hijau (Bagus)
            $statusText = 'NORMAL';
            $pulse = false;

            // Logika Warna Andon
            if ($dt > 60) {
                // Downtime > 60 menit -> MERAH BERKEDIP (Critical)
                $colorClass = 'bg-rose-700 border-rose-500 text-white';
                $statusText = 'DOWNTIME TINGGI';
                $pulse = true;
            } elseif ($eff < 70) {
                // Efisiensi < 70% -> MERAH (Masalah Performa)
                $colorClass = 'bg-rose-600 border-rose-500 text-white';
                $statusText = 'LOW EFFICIENCY';
            } elseif ($eff < 90) {
                // Efisiensi 70-90% -> KUNING/ORANGE (Warning)
                $colorClass = 'bg-amber-600 border-amber-500 text-white';
                $statusText = 'WARNING';
            }

            return [
                'id' => $machine->id,
                'name' => $machine->name,
                'status' => 'running',
                'color_class' => $colorClass,
                'pulse' => $pulse ?? false,
                'product' => $report->product->name,
                'batch' => $report->batch->batch_code,
                'target' => $report->qty_theory,
                'actual' => $report->total_output,
                'efficiency' => $eff,
                'downtime' => $dt,
                'message' => $statusText
            ];
        });

        return view('production.andon.index', compact('andonData', 'today'));
    }
}
