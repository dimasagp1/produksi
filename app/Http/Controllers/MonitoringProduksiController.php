<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Machine;
use App\Models\DailyReport;
use Carbon\Carbon;

class MonitoringProduksiController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Ambil semua mesin aktif
        $machines = Machine::where('status', 'active')->get();

        // Ambil laporan hari ini untuk cek status
        $monitoringData = $machines->map(function ($machine) use ($today) {
            // Cek apakah ada laporan produksi hari ini untuk mesin ini
            // Ambil yang terakhir diupdate
            $report = DailyReport::with(['product', 'operator', 'batch'])
                ->where('machine_id', $machine->id)
                ->whereDate('production_date', $today)
                ->orderBy('updated_at', 'desc')
                ->first();

            $status = 'idle'; // Default idle
            $data = null;

            if ($report) {
                // Jika ada laporan, asumsikan sedang produksi (Running)
                // Kecuali jika status verified (Selesai) atau ada downtime aktif (opsional logic)
                $status = ($report->status === 'verified') ? 'completed' : 'running';

                $data = [
                    'product' => $report->product->name ?? '-',
                    'batch' => $report->batch->batch_code ?? '-',
                    'operator' => $report->operator->name ?? '-',
                    'output' => $report->total_output ?? 0,
                    'reject' => $report->qty_reject_total ?? 0,
                    'efficiency' => $report->efficiency ?? 0,
                    'last_update' => $report->updated_at->diffForHumans()
                ];
            }

            return [
                'machine_name' => $machine->name,
                'status' => $status,
                'data' => $data
            ];
        });

        // Hitung Ringkasan
        $summary = [
            'total_mesin' => $machines->count(),
            'running' => $monitoringData->where('status', 'running')->count(),
            'idle' => $monitoringData->where('status', 'idle')->count(),
        ];

        return view('production.monitoring-produksi.index', compact('monitoringData', 'summary'));
    }
}
