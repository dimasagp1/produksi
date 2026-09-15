<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DailyReport;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\DashboardFullExport;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($request->get('export') === 'excel') {
            return $this->exportExcel($request);
        }

        if ($request->get('export') === 'pdf') {
            return $this->exportPdf($request);
        }

        $data = $this->getDashboardData($request);
        return view('dashboard', $data);
    }

    public function tvMode(Request $request)
    {
        if (!$request->has('start_date')) {
            $request->merge(['start_date' => now()->startOfMonth()->format('Y-m-d')]);
        }
        if (!$request->has('end_date')) {
            $request->merge(['end_date' => now()->format('Y-m-d')]);
        }

        $data = $this->getDashboardData($request);
        return view('dashboard_tv', $data);
    }

    public function exportExcel(Request $request)
    {
        $data = $this->getDashboardData($request);
        $fileName = 'Laporan_Produksi_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new DashboardFullExport($data), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getDashboardData($request);
        $fileName = 'Laporan_Produksi_' . date('Ymd_His') . '.pdf';
        $pdf = Pdf::loadView('exports.dashboard_full_pdf', $data);
        $pdf->setPaper('A4', 'landscape');
        return $pdf->download($fileName);
    }

    public function getDashboardJson(Request $request)
    {
        $data = $this->getDashboardData($request);

        $kpi = [
            'total_target'    => number_format($data['totalTarget'], 0, ',', '.'),
            'achievement'    => number_format($data['achievement'], 2) . '%',
            'achievement_val' => $data['achievement'],
            'total_output'    => number_format($data['totalOutput'], 0, ',', '.'),
            'total_reject'    => number_format($data['totalReject'], 0, ',', '.'),
            'avg_yield'       => number_format($data['avgYield'], 2) . '%',
            'avg_efficiency' => number_format($data['avgEfficiency'], 2) . '%',
            'eff_val'        => $data['avgEfficiency'],
        ];

        $chartTrend = [
            'labels' => $data['trendOutput']->pluck('label'),
            'output' => $data['trendOutput']->pluck('total'),
            'target' => $data['trendOutput']->pluck('total_target'),
        ];

        $chartMachine = [];
        $chartRejectMachine = [];

        if ($data['isFullAccess']) {
            $chartMachine['categories'] = $data['topMachineProducts']->map(function ($item) {
                $lines = [$item->machine_name];
                $productLines = explode("\n", wordwrap($item->product_name, 25, "\n"));
                return array_merge($lines, $productLines);
            })->values();
            $chartMachine['series'] = $data['topMachineProducts']->pluck('total');

            $chartRejectMachine['labels'] = $data['rejectByMachine']->pluck('label');
            $chartRejectMachine['series'] = $data['rejectByMachine']->pluck('total');
        }

        return response()->json([
            'kpi' => $kpi,
            'charts' => [
                'trend' => $chartTrend,
                'machine' => $chartMachine,
                'reject_machine' => $chartRejectMachine
            ]
        ]);
    }

    private function getDashboardData(Request $request)
    {
        // --- A. FILTER TANGGAL ---
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::now()->startOfMonth()->startOfDay();
        $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();

        if ($startDate->gt($endDate)) $startDate = $endDate->copy()->startOfDay();

        $startStr = $startDate->format('Y-m-d H:i:s');
        $endStr = $endDate->format('Y-m-d H:i:s');
        $productId = $request->product_id;

        // --- CEK ROLE ---
        $user = auth()->user();
        if (!$user) abort(403, 'Unauthorized action.');

        $userRole = $user->role;
        $isFullAccess = in_array($userRole, ['admin', 'manager', 'super_admin']);

        // --- RUMUS SQL DINAMIS UNTUK QTY THEORY ---
        // Menggabungkan waktu, downtime, cycle time standar pivot, dan cavity
        $theoryCalcSql = 'FLOOR( ( (IFNULL(daily_reports.total_minutes, 0) - IFNULL(daily_reports.downtime_total, 0)) * 60 / COALESCE(NULLIF(machine_product.cycle_time, 0), NULLIF(daily_reports.actual_cycle_time, 0), 1) ) * COALESCE(NULLIF(machine_product.cavity, 0), NULLIF(daily_reports.actual_cavity, 0), 1) )';

        // --- QUERY DATA DASAR DENGAN JOIN MASTER ---
        $reportsQuery = DailyReport::leftJoin('machine_product', function ($join) {
            $join->on('daily_reports.product_id', '=', 'machine_product.product_id')
                ->on('daily_reports.machine_id', '=', 'machine_product.machine_id');
        })
            ->whereBetween('daily_reports.production_date', [$startStr, $endStr]);

        if ($productId) {
            $reportsQuery->where('daily_reports.product_id', $productId);
        }

        // --- B. DATA SUMMARY ---
        $totalOutput = $reportsQuery->sum('daily_reports.total_output');
        $totalReject = $reportsQuery->sum('daily_reports.qty_reject_total');

        // Target dihitung ulang secara dinamis, bukan sekadar SUM column
        $totalTarget = $reportsQuery->sum(DB::raw($theoryCalcSql));
        $totalGood = $reportsQuery->sum('daily_reports.qty_good');

        $achievement = $totalTarget > 0 ? ($totalOutput / $totalTarget) * 100 : 0;
        $avgYield = $totalOutput > 0 ? ($totalGood / $totalOutput) * 100 : 0;
        $avgEfficiency = $totalTarget > 0 ? ($totalGood / $totalTarget) * 100 : 0;

        // --- C. LOGIKA GRAFIK TREN ---
        $diffInDays = $startDate->diffInDays($endDate);

        $trendQuery = DailyReport::leftJoin('machine_product', function ($join) {
            $join->on('daily_reports.product_id', '=', 'machine_product.product_id')
                ->on('daily_reports.machine_id', '=', 'machine_product.machine_id');
        })
            ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
            ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId));

        if ($diffInDays > 31) {
            $rawTrend = $trendQuery
                ->selectRaw('DATE_FORMAT(daily_reports.production_date, "%Y-%m-01") as date_key, SUM(daily_reports.total_output) as total, SUM(daily_reports.qty_reject_total) as total_reject, SUM(' . $theoryCalcSql . ') as total_target')
                ->groupBy('date_key')->orderBy('date_key')->get();
        } else {
            $rawTrend = $trendQuery
                ->selectRaw('DATE(daily_reports.production_date) as date_key, SUM(daily_reports.total_output) as total, SUM(daily_reports.qty_reject_total) as total_reject, SUM(' . $theoryCalcSql . ') as total_target')
                ->groupBy('date_key')->orderBy('date_key')->get();
        }

        $mappedRaw = $rawTrend->mapWithKeys(function ($item) {
            return [$item->date_key => $item];
        });

        if ($diffInDays > 31) {
            $trendOutput = $rawTrend->map(function ($item) {
                return [
                    'label' => Carbon::parse($item->date_key)->format('M Y'),
                    'date' => $item->date_key,
                    'total' => (int) $item->total,
                    'total_target' => (int) ($item->total_target ?? 0),
                    'total_reject' => (int) ($item->total_reject ?? 0)
                ];
            })->values();
        } else {
            $period = CarbonPeriod::create($startDate, $endDate);
            $trendOutput = collect([]);

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                $data = $mappedRaw->get($dateStr);

                $trendOutput->push([
                    'label' => $date->format('d M'),
                    'date' => $dateStr,
                    'total' => $data ? (int) $data->total : 0,
                    'total_target' => $data ? (int) ($data->total_target ?? 0) : 0,
                    'total_reject' => $data ? (int) ($data->total_reject ?? 0) : 0
                ]);
            }
        }

        // --- D. DATA TABEL (YANG SEBELUMNYA SALAH AMBIL DATA) ---
        $rawTrendTableData = DailyReport::join('products', 'daily_reports.product_id', '=', 'products.id')
            ->leftJoin('machine_product', function ($join) {
                $join->on('daily_reports.product_id', '=', 'machine_product.product_id')
                    ->on('daily_reports.machine_id', '=', 'machine_product.machine_id');
            })
            ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
            ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
            ->selectRaw("
                DATE(daily_reports.production_date) as date, 
                products.name as product_name, 
                SUM({$theoryCalcSql}) as sum_theory, 
                SUM(daily_reports.total_output) as sum_output, 
                SUM(daily_reports.qty_good) as sum_good, 
                SUM(daily_reports.qty_reject_total) as sum_reject
            ")
            ->groupBy(DB::raw('DATE(daily_reports.production_date)'), 'products.name')
            ->orderBy('products.name', 'asc')
            ->orderBy('date', 'desc')
            ->get();

        $trendTableData = $rawTrendTableData->groupBy('product_name');
        $productsList = Product::orderBy('name')->get();

        $topRejects = collect([]);
        $topMachineProducts = collect([]);
        $totalTopRejectsQty = 0;
        $topDowntimes = collect([]);
        $totalTopDowntimeMinutes = 0;
        $rejectByRejectItem = collect([]);
        $rejectByMachine = collect([]);
        $rejectByOperator = collect([]);
        $rejectByCoordinator = collect([]);
        $rejectByShift = collect([]);
        $detailRejects = collect([]);
        $totalDetailRejectsQty = 0;
        $operatorPerformance = collect([]);
        $rejectScatterData = collect([]);

        // --- E. DATA DETAIL (FULL ACCESS) ---
        if ($isFullAccess) {
            $topMachineProducts = DB::table('daily_reports')
                ->join('machines', 'daily_reports.machine_id', '=', 'machines.id')
                ->join('products', 'daily_reports.product_id', '=', 'products.id')
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->selectRaw('machines.name as machine_name, products.name as product_name, SUM(daily_reports.total_output) as total')
                ->groupBy('machines.name', 'products.name')
                ->orderByDesc('total')->limit(10)->get();

            $operatorPerformance = DB::table('daily_reports')
                ->join('operators', 'daily_reports.operator_id', '=', 'operators.id')
                ->leftJoin('machine_product', function ($join) {
                    $join->on('daily_reports.product_id', '=', 'machine_product.product_id')
                        ->on('daily_reports.machine_id', '=', 'machine_product.machine_id');
                })
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->selectRaw("
                    operators.name as operator_name, 
                    SUM(daily_reports.total_output) as actual_output, 
                    SUM({$theoryCalcSql}) as total_target, 
                    SUM(daily_reports.qty_good) as actual_fg, 
                    (SUM(daily_reports.total_output) / NULLIF(SUM({$theoryCalcSql}), 0)) * 100 as output_rate, 
                    (SUM(daily_reports.qty_good) / NULLIF(SUM({$theoryCalcSql}), 0)) * 100 as fg_rate
                ")
                ->groupBy('operators.name')->orderByDesc('output_rate')->limit(10)->get();

            $topRejects = DB::table('daily_report_rejects')
                ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
                ->join('reject_items', 'daily_report_rejects.reject_item_id', '=', 'reject_items.id')
                ->join('products', 'daily_reports.product_id', '=', 'products.id')
                ->join('machines', 'daily_reports.machine_id', '=', 'machines.id')
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->selectRaw('reject_items.name as reject_name, products.name as product_name, machines.name as machine_name, SUM(daily_report_rejects.qty) as total_qty')
                ->groupBy('reject_items.name', 'products.name', 'machines.name')->orderByDesc('total_qty')->limit(10)->get();
            $totalTopRejectsQty = $topRejects->sum('total_qty');

            $topDowntimes = DB::table('daily_report_downtimes')
                ->join('daily_reports', 'daily_report_downtimes.daily_report_id', '=', 'daily_reports.id')
                ->join('downtimes', 'daily_report_downtimes.downtime_id', '=', 'downtimes.id')
                ->join('machines', 'daily_reports.machine_id', '=', 'machines.id')
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->selectRaw('machines.name as machine_name, downtimes.name as downtime_reason, SUM(daily_report_downtimes.duration) as total_minutes')
                ->groupBy('machines.name', 'downtimes.name')->orderByDesc('total_minutes')->limit(10)->get();
            $totalTopDowntimeMinutes = $topDowntimes->sum('total_minutes');

            $getRejectStats = function ($table, $fk_column, $limit = 5) use ($startStr, $endStr, $productId) {
                return DB::table('daily_report_rejects')
                    ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
                    ->join($table, "daily_reports.$fk_column", '=', "$table.id")
                    ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                    ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                    ->selectRaw("$table.name as label, SUM(daily_report_rejects.qty) as total")
                    ->groupBy("$table.name")->orderByDesc('total')->limit($limit)->get();
            };

            $rejectByRejectItem = DB::table('daily_report_rejects')
                ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
                ->join('reject_items', 'daily_report_rejects.reject_item_id', '=', 'reject_items.id')
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->selectRaw("reject_items.name as label, SUM(daily_report_rejects.qty) as total")
                ->groupBy("reject_items.name")->orderByDesc('total')->limit(5)->get();

            $rejectByMachine = $getRejectStats('machines', 'machine_id', 10);
            $rejectByOperator = $getRejectStats('operators', 'operator_id');
            $rejectByCoordinator = $getRejectStats('coordinators', 'coordinator_id');
            $rejectByShift = $getRejectStats('shifts', 'shift_id');

            $detailRejects = DB::table('daily_report_rejects')
                ->join('daily_reports', 'daily_report_rejects.daily_report_id', '=', 'daily_reports.id')
                ->join('reject_items', 'daily_report_rejects.reject_item_id', '=', 'reject_items.id')
                ->join('products', 'daily_reports.product_id', '=', 'products.id')
                ->join('machines', 'daily_reports.machine_id', '=', 'machines.id')
                ->join('operators', 'daily_reports.operator_id', '=', 'operators.id')
                ->join('coordinators', 'daily_reports.coordinator_id', '=', 'coordinators.id')
                ->join('shifts', 'daily_reports.shift_id', '=', 'shifts.id')
                ->join('batches', 'daily_reports.batch_id', '=', 'batches.id')
                ->whereBetween('daily_reports.production_date', [$startStr, $endStr])
                ->when($productId, fn($q) => $q->where('daily_reports.product_id', $productId))
                ->select('daily_reports.production_date', 'products.name as product_name', 'reject_items.name as reject_name', 'machines.name as machine_name', 'operators.name as operator_name', 'coordinators.name as coordinator_name', 'shifts.name as shift_name', 'batches.batch_code as batch_name', 'daily_report_rejects.qty')
                ->orderByDesc('daily_report_rejects.qty')->limit(10)->get();
            $totalDetailRejectsQty = $detailRejects->sum('qty');
        }

        return [
            'isFullAccess' => $isFullAccess,
            'userRole' => $userRole,
            'userName' => $user->name,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'productId' => $productId,
            'productsList' => $productsList,
            'totalOutput' => $totalOutput,
            'totalReject' => $totalReject,
            'avgYield' => $avgYield,
            'avgEfficiency' => $avgEfficiency,
            'totalTarget' => $totalTarget,
            'achievement' => $achievement,
            'trendOutput' => $trendOutput,
            'trendTableData' => $trendTableData,
            'diffInDays' => $diffInDays,
            'topMachineProducts' => $topMachineProducts,
            'operatorPerformance' => $operatorPerformance,
            'topRejects' => $topRejects,
            'totalTopRejectsQty' => $totalTopRejectsQty,
            'topDowntimes' => $topDowntimes,
            'totalTopDowntimeMinutes' => $totalTopDowntimeMinutes,
            'rejectByRejectItem' => $rejectByRejectItem,
            'rejectByMachine' => $rejectByMachine,
            'rejectByOperator' => $rejectByOperator,
            'rejectByCoordinator' => $rejectByCoordinator,
            'rejectByShift' => $rejectByShift,
            'rejectScatterData' => $rejectScatterData,
            'detailRejects' => $detailRejects,
            'totalDetailRejectsQty' => $totalDetailRejectsQty,
            'details' => $detailRejects,
            'summary' => ['totalOutput' => $totalOutput, 'totalReject' => $totalReject, 'avgYield' => $avgYield, 'avgEfficiency' => $avgEfficiency]
        ];
    }
}
