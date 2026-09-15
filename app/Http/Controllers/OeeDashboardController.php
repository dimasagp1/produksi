<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OeeDashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Tanggal dari Request (Bisa Null)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // 2. Query Data Laporan
        $query = DailyReport::with(['machine']);

        // Terapkan filter HANYA JIKA tanggal diisi
        if ($startDate && $endDate) {
            $query->whereDate('production_date', '>=', $startDate)
                ->whereDate('production_date', '<=', $endDate);
        }

        $reports = $query->get();

        // 3. Hitung Metrik Global (Rata-rata Tertimbang)
        $totalPlannedTime = $reports->sum('total_minutes');
        $totalDowntime = $reports->sum('downtime_total');
        $totalOperatingTime = max(0, $totalPlannedTime - $totalDowntime);

        $totalOutput = $reports->sum('total_output');
        $totalGood = $reports->sum('qty_good');
        $totalTheory = $reports->sum('qty_theory');

        // --- CALCULATE OEE COMPONENTS ---

        // A. Availability
        $availability = ($totalPlannedTime > 0) ? ($totalOperatingTime / $totalPlannedTime) * 100 : 0;

        // B. Performance
        $performance = ($totalTheory > 0) ? ($totalOutput / $totalTheory) * 100 : 0;

        // C. Quality
        $quality = ($totalOutput > 0) ? ($totalGood / $totalOutput) * 100 : 0;

        // D. OEE Score
        $oeeScore = ($availability * $performance * $quality) / 10000;

        // 4. Data Per Mesin
        $machineStats = $reports->groupBy('machine_id')->map(function ($machineReports) {
            $mPlanned = $machineReports->sum('total_minutes');
            $mDown = $machineReports->sum('downtime_total');
            $mOp = max(0, $mPlanned - $mDown);

            $mOut = $machineReports->sum('total_output');
            $mGood = $machineReports->sum('qty_good');
            $mTheory = $machineReports->sum('qty_theory');

            $mAvail = ($mPlanned > 0) ? ($mOp / $mPlanned) * 100 : 0;
            $mPerf = ($mTheory > 0) ? ($mOut / $mTheory) * 100 : 0;
            $mQual = ($mOut > 0) ? ($mGood / $mOut) * 100 : 0;
            $mOee = ($mAvail * $mPerf * $mQual) / 10000;

            return (object) [
                'name' => $machineReports->first()->machine->name ?? 'Unknown',
                'availability' => $mAvail,
                'performance' => $mPerf,
                'quality' => $mQual,
                'oee' => $mOee,
                'total_output' => $mOut,
                'downtime' => $mDown
            ];
        })->sortByDesc('oee');

        return view('production.oee-dashboard.index', compact(
            'startDate',
            'endDate',
            'availability',
            'performance',
            'quality',
            'oeeScore',
            'machineStats'
        ));
    }
}
