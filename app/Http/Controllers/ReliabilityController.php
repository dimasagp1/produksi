<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\BreakdownReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReliabilityController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Hitung Total Waktu Tersedia (Planned Production Time)
        // Asumsi: Operasi 24 Jam x Jumlah Hari dalam periode
        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        $days = $start->diffInDays($end) + 1;
        $totalMinutesAvailable = $days * 24 * 60;

        // 2. Ambil Data Mesin & Breakdown
        $machines = Machine::where('status', 'active')->get();

        $analysisData = $machines->map(function ($machine) use ($startDate, $endDate, $totalMinutesAvailable) {
            // Ambil breakdown pada range tanggal ini
            $breakdowns = BreakdownReport::where('machine_id', $machine->id)
                ->whereDate('breakdown_time', '>=', $startDate)
                ->whereDate('breakdown_time', '<=', $endDate)
                ->get();

            $freq = $breakdowns->count(); // Jumlah Kerusakan
            $totalDowntime = $breakdowns->sum('downtime_minutes'); // Total Mati (Menit)
            $totalUptime = $totalMinutesAvailable - $totalDowntime; // Total Hidup (Menit)

            // Rumus MTBF = Total Uptime / Jumlah Kerusakan
            // Jika tidak ada kerusakan, MTBF = Total Waktu Periode
            $mtbf = $freq > 0 ? ($totalUptime / 60) / $freq : ($totalUptime / 60); // Dalam JAM

            // Rumus MTTR = Total Downtime / Jumlah Kerusakan
            $mttr = $freq > 0 ? $totalDowntime / $freq : 0; // Dalam MENIT

            return [
                'name' => $machine->name,
                'freq' => $freq,
                'downtime' => $totalDowntime,
                'mtbf' => round($mtbf, 1), // Jam
                'mttr' => round($mttr, 1)  // Menit
            ];
        });

        // 3. Sorting untuk Chart (Top 5 Worst MTBF - Paling sering rusak)
        $chartData = $analysisData->sortBy('mtbf')->take(5);

        return view('engineering.reliability.index', compact(
            'analysisData',
            'chartData',
            'startDate',
            'endDate',
            'days'
        ));
    }
}
