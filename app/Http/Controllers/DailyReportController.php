<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use App\Models\Batch;
use App\Models\Machine;
use App\Models\Shift;
use App\Models\Coordinator;
use App\Models\Operator;
use App\Models\RejectItem;
use App\Models\Downtime;
use App\Models\PackagingType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Exports\DailyReportExport;
use App\Exports\DailyReportTemplateExport;
use App\Imports\DailyReportImport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DailyReportController extends Controller
{
    /**
     * 1. HALAMAN UTAMA (INDEX)
     */
    public function index(Request $request)
    {
        // --- STATISTIK DASHBOARD ---
        $today = Carbon::today();
        $statTotalReports = DailyReport::count();
        $statTodayReports = DailyReport::whereDate('production_date', $today)->count();
        $statTotalDowntime = DailyReport::sum('downtime_total');
        $statTotalReject = DailyReport::sum('qty_reject_total');
        $statTotalVerified = DailyReport::where('status', 'verified')->count();
        $statTotalActive = DailyReport::where('status', '!=', 'verified')->count();
        $totalOutputToday = DailyReport::whereDate('production_date', $today)->sum('total_output');
        $avgEfficiency = DailyReport::whereDate('production_date', $today)->avg('efficiency') ?? 0;

        // --- FILTERING ---
        // Join ke machine_product untuk dapat data Master (Standar)
        $query = DailyReport::query()
            ->select([
                'daily_reports.*',
                'mp.cycle_time as master_cycle_time',
                'mp.cavity as master_cavity'
            ])
            ->leftJoin('machine_product as mp', function ($join) {
                $join->on('daily_reports.machine_id', '=', 'mp.machine_id')
                    ->on('daily_reports.product_id', '=', 'mp.product_id');
            })
            ->with(['batch', 'machine', 'shift', 'product', 'operator', 'coordinator']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('report_code', 'LIKE', "%{$search}%")
                    ->orWhereHas('batch', fn($b) => $b->where('batch_code', 'LIKE', "%{$search}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('machine', fn($m) => $m->where('name', 'LIKE', "%{$search}%"));
            });
        }
        if ($request->filled('filter_shift')) {
            $query->where('shift_id', $request->filter_shift);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('production_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('production_date', '<=', $request->end_date);
        }
        if ($request->filled('filter_status')) {
            if ($request->filter_status === 'verified') {
                $query->where('status', 'verified');
            } elseif ($request->filter_status === 'active') {
                $query->where('status', '!=', 'verified');
            }
        }

        $reports = $query->orderBy('production_date', 'desc')->orderBy('created_at', 'desc')->paginate(10);
        $reports->appends($request->all());

        $shifts = Shift::where('status', 'active')->get();

        $machineRejectsRaw = DB::table('machine_reject_item')->get();
        $machineRejects = [];
        foreach ($machineRejectsRaw as $row) {
            $machineRejects[$row->machine_id][] = $row->reject_item_id;
        }

        return view('production.daily-reports.index', compact(
            'statTotalReports',
            'statTodayReports',
            'statTotalDowntime',
            'statTotalReject',
            'statTotalVerified',
            'statTotalActive',
            'totalOutputToday',
            'avgEfficiency',
            'reports',
            'shifts',
            'machineRejects'
        ));
    }

    /**
     * 2. HALAMAN FORM CREATE
     */
    public function create()
    {
        $data = $this->getFormData();
        return view('production.daily-reports.form', $data);
    }

    /**
     * 3. SIMPAN DATA BARU
     */
    public function store(Request $request)
    {
        return $this->saveData($request, new DailyReport());
    }

    /**
     * 4. HALAMAN FORM EDIT
     */
    public function edit($id)
    {
        $report = DailyReport::with(['rejects', 'downtimes'])->findOrFail($id);
        $user = Auth::user();

        if ($report->status === 'verified') {
            return redirect()->route('daily-reports.index')->with('error', 'Laporan terkunci (Status Final).');
        }

        $isExpired = Carbon::parse($report->production_date)->diffInDays(now()) > 2;
        if ($isExpired && !in_array($user->role, ['admin', 'super_admin'])) {
            return redirect()->route('daily-reports.index')->with('error', 'Batas waktu edit (H+2) telah habis.');
        }

        $data = $this->getFormData();
        $data['report'] = $report;

        return view('production.daily-reports.form', $data);
    }

    /**
     * 5. UPDATE DATA
     */
    public function update(Request $request, $id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        if ($report->status === 'verified') {
            return back()->with('error', 'Laporan terkunci (Status Final). Hubungi Admin untuk membuka.');
        }

        $isExpired = Carbon::parse($report->production_date)->diffInDays(now()) > 2;
        if ($isExpired && !in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Batas waktu edit (H+2) telah habis.');
        }

        $netGoodOld = max(0, $report->qty_good - $report->qty_sample);
        $this->revertBatchProgress($report->batch_id, $netGoodOld, $report->qty_reject_total);

        return $this->saveData($request, $report);
    }

    /**
     * 6. HALAMAN DETAIL (SHOW)
     */
    public function show($id)
    {
        $report = DailyReport::select([
            'daily_reports.*',
            'mp.cycle_time as master_cycle_time',
            'mp.cavity as master_cavity'
        ])
            ->leftJoin('machine_product as mp', function ($join) {
                $join->on('daily_reports.machine_id', '=', 'mp.machine_id')
                    ->on('daily_reports.product_id', '=', 'mp.product_id');
            })
            ->with(['batch', 'machine', 'shift', 'product', 'color', 'coordinator', 'operator', 'packagingType', 'rejects.rejectItem', 'downtimes.downtime'])
            ->where('daily_reports.id', $id)
            ->firstOrFail();

        if (request()->wantsJson()) {
            return response()->json($report);
        }

        return view('production.daily-reports.show', compact('report'));
    }

    /**
     * 7. HAPUS DATA
     */
    public function destroy($id)
    {
        $report = DailyReport::findOrFail($id);
        $user = Auth::user();

        if ($report->status === 'verified') return back()->with('error', 'Laporan terkunci.');

        $isExpired = Carbon::parse($report->production_date)->diffInDays(now()) > 2;
        if ($isExpired && !in_array($user->role, ['admin', 'super_admin'])) {
            return back()->with('error', 'Tidak bisa menghapus data lama (H+2).');
        }

        $netGood = max(0, $report->qty_good - $report->qty_sample);
        $this->revertBatchProgress($report->batch_id, $netGood, $report->qty_reject_total);
        $report->delete();

        return redirect()->route('daily-reports.index')->with('success', 'Laporan Berhasil Dihapus');
    }

    /**
     * 8. AJAX HELPER: GET BATCH DETAILS
     */
    public function getBatchDetails($id)
    {
        $batch = Batch::with(['product', 'color', 'machine'])->findOrFail($id);
        $machineId = $batch->machine_id;

        $cycleTime = 0;
        $actualCycleTime = 0;
        $cavity = 1;
        $actualCavity = 1;

        if ($machineId) {
            $pivot = DB::table('machine_product')
                ->where('product_id', $batch->product_id)
                ->where('machine_id', $machineId)
                ->first();

            if ($pivot) {
                $cycleTime = $pivot->cycle_time;
                $actualCycleTime = $pivot->actual_cycle_time;
                $cavity = $pivot->cavity;
                $actualCavity = $pivot->actual_cavity > 0 ? $pivot->actual_cavity : $pivot->cavity;
            }
        }

        $packagingTypeId = $batch->product->packaging_type_id ?? null;
        $packagingTypeName = '-';

        if ($packagingTypeId) {
            $pt = PackagingType::find($packagingTypeId);
            if ($pt) $packagingTypeName = $pt->name;
        }

        $productPackagingQty = $batch->product->packaging_qty ?? 0;
        $productWeight = $batch->product->weight ?? 0;

        $previousReport = DailyReport::where('batch_id', $id)
            ->when($machineId, fn($q) => $q->where('machine_id', $machineId))
            ->orderBy('production_date', 'desc')
            ->orderBy('shift_id', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
        $previousWip = $previousReport ? $previousReport->wip : 0;

        return response()->json([
            'product_id' => $batch->product_id,
            'product_name' => $batch->product->name ?? '-',
            'product_weight' => (float) $productWeight,
            'color_id' => $batch->color_id,
            'color_name' => $batch->color->name ?? '-',
            'machine_id' => $machineId,
            'packaging_type_id' => $packagingTypeId,
            'packaging_type_name' => $packagingTypeName,
            'product_packaging_qty' => (float) $productPackagingQty,
            'target_qty' => $batch->target_quantity,
            'current_qty' => $batch->current_quantity,
            'cycle_time' => (float) $cycleTime,
            'actual_cycle_time' => 0,
            'cavity' => (int) $cavity,
            'actual_cavity' => 0,
            'priority' => ucfirst($batch->priority),
            'previous_wip' => (int) $previousWip,
        ]);
    }

    /**
     * 9. FITUR LOCK
     */
    public function toggleLock($id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'super_admin'])) return back()->with('error', 'Akses ditolak.');

        $report = DailyReport::findOrFail($id);
        $report->status = ($report->status === 'verified') ? 'submitted' : 'verified';
        $report->save();

        $statusMsg = ($report->status === 'verified') ? 'Laporan Dikonfirmasi & Dikunci.' : 'Laporan Dibuka Kembali.';
        return back()->with('success', $statusMsg);
    }

    // --- PRIVATE METHODS ---

    private function getFormData()
    {
        $batches = Batch::where('is_active', 1)
            ->whereIn('status', ['running', 'planning'])
            ->orderBy('created_at', 'desc')->get();
        $machines = Machine::where('status', 'active')->get();
        $shifts = Shift::where('status', 'active')->get();
        $coordinators = Coordinator::where('status', 'active')->get();
        $operators = Operator::where('status', 'active')->get();
        $rejectItems = RejectItem::with('category')->where('status', 'active')->orderBy('category_id')->get();
        $downtimes = Downtime::where('status', 'active')->get();

        $machineRejectsRaw = DB::table('machine_reject_item')->get();
        $machineRejects = [];
        foreach ($machineRejectsRaw as $row) {
            $machineRejects[$row->machine_id][] = $row->reject_item_id;
        }

        return compact('batches', 'machines', 'shifts', 'coordinators', 'operators', 'rejectItems', 'downtimes', 'machineRejects');
    }

    private function saveData($request, $report)
    {
        DB::beginTransaction();
        try {
            $rawData = $request->all();

            $numericFields = [
                'qty_theory',
                'qty_good',
                'qty_reject_total',
                'qty_sample',
                'total_runner',
                'qty_purging',
                'purging_kg',
                'weight_per_pcs',
                'wip',
                'wip_previous',
                'total_counter',
                'total_output',
                'packaging_qty',
                'cycle_time',
                'actual_cycle_time',
                'cavity',
                'actual_cavity'
            ];

            foreach ($numericFields as $field) {
                $val = $rawData[$field] ?? 0;
                if ($val === '' || is_null($val)) {
                    $val = 0;
                } else {
                    $val = str_replace(['.', 'Rp', ' '], '', $val);
                    $val = str_replace(',', '.', $val);
                }
                $rawData[$field] = $val;
            }
            $request->merge($rawData);

            $request->validate([
                'production_date' => 'required|date',
                'batch_id' => 'required',
                'qty_good' => 'required|numeric|min:0',
                'actual_cavity' => 'required|numeric|min:1',
                'actual_cycle_time' => 'required|numeric|min:0.01',
            ]);

            $totalDowntime = 0;
            if ($request->has('downtimes')) {
                foreach ($request->downtimes as $dt) {
                    $dur = str_replace(['.', ','], ['', '.'], $dt['duration'] ?? 0);
                    $totalDowntime += (float) $dur;
                }
            }

            $totalReject = 0;
            if ($request->has('rejects')) {
                foreach ($request->rejects as $r) {
                    $val = str_replace(['.', ','], ['', '.'], $r['qty'] ?? 0);
                    $totalReject += (float) $val;
                }
            }

            // --- FORCE AMBIL DATA STANDARD DARI DB MASTER ---
            $pivotStd = DB::table('machine_product')
                ->where('machine_id', $request->machine_id)
                ->where('product_id', $request->product_id)
                ->first();

            $ctStdFix = $pivotStd ? $pivotStd->cycle_time : (float) $rawData['cycle_time'];
            $cavityStdFix = $pivotStd ? $pivotStd->cavity : (int) $rawData['cavity'];

            $qtyGood = (float) $rawData['qty_good'];
            $qtySample = (float) $rawData['qty_sample'];
            $ctAct = (float) $rawData['actual_cycle_time'];
            $cavityAct = (int) $rawData['actual_cavity'];

            $start = Carbon::parse($request->production_date . ' ' . $request->start_time);
            $end = Carbon::parse($request->production_date . ' ' . $request->end_time);
            if ($end->lessThan($start)) $end->addDay();
            $totalMinutes = abs($end->diffInMinutes($start));

            $effectiveMinutes = max(0, $totalMinutes - $totalDowntime);

            // Gunakan CT Standard untuk perhitungan Target
            $ctCalc = $ctStdFix > 0 ? $ctStdFix : $ctAct;
            $qtyTheory = ($ctCalc > 0 && $effectiveMinutes > 0) ? floor(($effectiveMinutes * 60 / $ctCalc) * $cavityAct) : 0;

            $totalOutput = $qtyGood + $totalReject;
            $yield = ($totalOutput > 0) ? ($qtyGood / $totalOutput) * 100 : 0;
            $efficiency = ($qtyTheory > 0) ? ($qtyGood / $qtyTheory) * 100 : 0;
            if ($efficiency > 999.99) $efficiency = 999.99;

            if (!$report->exists) $report->report_code = 'DR-' . date('ymd') . '-' . rand(100, 999);

            $report->fill($request->except(['rejects', 'downtimes']));
            $report->qty_sample = $qtySample;
            $report->qty_reject_total = $totalReject;
            $report->downtime_total = $totalDowntime;
            $report->total_minutes = $totalMinutes;
            $report->qty_theory = $qtyTheory;
            $report->total_output = $totalOutput;
            $report->qty_actual = $totalOutput;
            $report->efficiency = $efficiency;
            $report->yield = $yield;

            // --- SIMPAN NILAI YANG BENAR ---
            $report->cycle_time = $ctStdFix;        // 24.4
            $report->cavity = $cavityStdFix;
            $report->actual_cycle_time = $ctAct;    // 34.9
            $report->actual_cavity = $cavityAct;

            $report->save();

            if ($report->wasRecentlyCreated === false) {
                $report->rejects()->delete();
                $report->downtimes()->delete();
            }

            if ($request->has('rejects')) {
                foreach ($request->rejects as $r) {
                    $qty = str_replace(['.', ','], ['', '.'], $r['qty'] ?? 0);
                    if ($qty > 0) $report->rejects()->create(['reject_item_id' => $r['id'], 'qty' => $qty]);
                }
            }

            if ($request->has('downtimes')) {
                foreach ($request->downtimes as $dt) {
                    $dur = str_replace(['.', ','], ['', '.'], $dt['duration'] ?? 0);
                    if ($dur > 0) $report->downtimes()->create(['downtime_id' => $dt['id'], 'duration' => $dur, 'remarks' => $dt['remarks'] ?? null]);
                }
            }

            $updateData = [];
            if ($ctAct > 0) $updateData['actual_cycle_time'] = $ctAct;
            if ($cavityAct > 0) $updateData['actual_cavity'] = $cavityAct;

            if (!empty($updateData)) {
                DB::table('machine_product')
                    ->where('machine_id', $report->machine_id)
                    ->where('product_id', $report->product_id)
                    ->update($updateData);
            }

            $netGood = max(0, $qtyGood - $qtySample);
            $this->updateBatchProgress($report->batch_id, $netGood, $totalReject);

            DB::commit();
            return redirect()->route('daily-reports.edit')->with('success', 'Laporan Berhasil Disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    private function updateBatchProgress($bid, $netGood, $reject)
    {
        $b = Batch::find($bid);
        if ($b) {
            $b->current_quantity += $netGood;
            $b->reject_quantity += $reject;
            if ($b->current_quantity >= $b->target_quantity && $b->status != 'completed') $b->status = 'completed';
            elseif ($b->status == 'planning') $b->status = 'running';
            $b->save();
        }
    }

    private function revertBatchProgress($bid, $netGood, $reject)
    {
        $b = Batch::find($bid);
        if ($b) {
            $b->current_quantity -= $netGood;
            $b->reject_quantity -= $reject;
            $b->save();
        }
    }

    // --- EXPORT FUNCTIONS ---
    public function exportExcel(Request $r)
    {
        // 1. Ambil Query dengan Join Master Data
        $query = DailyReport::query()
            ->select([
                'daily_reports.*',
                'mp.cycle_time as master_cycle_time',
                'mp.cavity as master_cavity'
            ])
            ->leftJoin('machine_product as mp', function ($join) {
                $join->on('daily_reports.machine_id', '=', 'mp.machine_id')
                    ->on('daily_reports.product_id', '=', 'mp.product_id');
            })
            ->with([
                'batch.product',
                'batch.color',
                'shift',
                'machine',
                'operator',
                'coordinator',
                'packagingType',
                'rejects.rejectItem',
                'downtimes.downtime'
            ]);

        // Filter Logic
        if ($r->filled('search')) {
            $s = $r->search;
            $query->where(function ($q2) use ($s) {
                $q2->where('report_code', 'LIKE', "%{$s}%")
                    ->orWhereHas('batch', fn($b) => $b->where('batch_code', 'LIKE', "%{$s}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$s}%"))
                    ->orWhereHas('machine', fn($m) => $m->where('name', 'LIKE', "%{$s}%"));
            });
        }
        if ($r->filled('filter_shift')) $query->where('shift_id', $r->filter_shift);
        if ($r->filled('start_date')) $query->whereDate('production_date', '>=', $r->start_date);
        if ($r->filled('end_date')) $query->whereDate('production_date', '<=', $r->end_date);

        // 2. Ambil Master Rejects & Downtime untuk Header Dinamis
        $masterRejects = RejectItem::where('status', 'active')->orderBy('name')->get();
        $masterDowntimes = Downtime::where('status', 'active')->orderBy('name')->get();

        // 3. Download Excel via Class
        return Excel::download(new DailyReportExport($query, $masterRejects, $masterDowntimes), 'Laporan_Produksi.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new DailyReportTemplateExport, 'template_laporan.xlsx');
    }
    public function importExcel(Request $r)
    {
        $r->validate(['file' => 'required|mimes:xlsx,xls']);
        Excel::import(new DailyReportImport, $r->file('file'));
        return back()->with('success', 'Import Berhasil');
    }
    public function exportPdf(Request $r)
    {
        $q = DailyReport::with(['batch', 'machine', 'shift', 'product', 'operator', 'coordinator']);
        if ($r->filled('search')) {
            $s = $r->search;
            $q->where(function ($q2) use ($s) {
                $q2->where('report_code', 'LIKE', "%{$s}%")
                    ->orWhereHas('batch', fn($b) => $b->where('batch_code', 'LIKE', "%{$s}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$s}%"))
                    ->orWhereHas('machine', fn($m) => $m->where('name', 'LIKE', "%{$s}%"));
            });
        }
        if ($r->filled('filter_shift')) $q->where('shift_id', $r->filter_shift);
        if ($r->filled('start_date')) $q->whereDate('production_date', '>=', $r->start_date);
        if ($r->filled('end_date')) $q->whereDate('production_date', '<=', $r->end_date);

        $reports = $q->orderBy('production_date', 'desc')->get();
        return Pdf::loadView('exports.report', compact('reports'))->setPaper('a4', 'landscape')->download('Laporan_Produksi.pdf');
    }
}
