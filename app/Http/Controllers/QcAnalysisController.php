<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QcAnalysisController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal (Default: Bulan Ini)
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // ==========================================
        // A. PARETO ANALYSIS (Top Defect)
        // ==========================================
        // Query ini sudah benar karena mengambil dari tabel detail (daily_report_rejects)
        $rawPareto = DB::table('daily_report_rejects as drr')
            ->join('reject_items as ri', 'drr.reject_item_id', '=', 'ri.id')
            ->join('daily_reports as dr', 'drr.daily_report_id', '=', 'dr.id')
            ->whereDate('dr.production_date', '>=', $startDate)
            ->whereDate('dr.production_date', '<=', $endDate)
            ->select('ri.name', DB::raw('SUM(drr.qty) as total_qty'))
            ->groupBy('ri.id', 'ri.name')
            ->orderByDesc('total_qty')
            ->get();

        // Hitung Kumulatif % untuk Garis Pareto
        $grandTotalReject = $rawPareto->sum('total_qty');
        $cumulativeSum = 0;
        $paretoChartData = [
            'labels' => [],
            'data_bar' => [], // Jumlah Reject
            'data_line' => [] // % Kumulatif
        ];

        foreach ($rawPareto as $item) {
            $cumulativeSum += $item->total_qty;
            $percentage = $grandTotalReject > 0 ? ($cumulativeSum / $grandTotalReject) * 100 : 0;

            $paretoChartData['labels'][] = $item->name;
            $paretoChartData['data_bar'][] = $item->total_qty;
            $paretoChartData['data_line'][] = round($percentage, 1);
        }

        // ==========================================
        // B. TREND ANALYSIS (Reject Rate Daily)
        // ==========================================
        // PERBAIKAN DI SINI: Menggunakan (total_output - qty_good) karena kolom qty_reject tidak ada
        $rawTrend = DailyReport::whereDate('production_date', '>=', $startDate)
            ->whereDate('production_date', '<=', $endDate)
            ->groupBy('production_date')
            ->selectRaw('production_date, SUM(total_output) as total_output, SUM(total_output - qty_good) as total_reject')
            ->orderBy('production_date', 'asc')
            ->get();

        $trendChartData = [
            'labels' => [],
            'rate' => [],
            'qty' => []
        ];

        foreach ($rawTrend as $day) {
            // Reject Rate = (Reject / Total Output) * 100
            $rate = $day->total_output > 0 ? ($day->total_reject / $day->total_output) * 100 : 0;

            $trendChartData['labels'][] = Carbon::parse($day->production_date)->format('d M');
            $trendChartData['rate'][] = round($rate, 2);
            $trendChartData['qty'][] = (int) $day->total_reject;
        }

        return view('qc.analysis.index', compact(
            'startDate',
            'endDate',
            'paretoChartData',
            'trendChartData',
            'grandTotalReject'
        ));
    }
}
