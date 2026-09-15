<?php

namespace App\Http\Controllers;

use App\Models\BreakdownReport;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BreakdownController extends Controller
{
    public function index(Request $request)
    {
        // Filter Bulan Berjalan
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = BreakdownReport::with('machine')
            ->whereDate('breakdown_time', '>=', $startDate)
            ->whereDate('breakdown_time', '<=', $endDate);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('report_code', 'like', "%{$s}%")
                    ->orWhereHas('machine', fn($m) => $m->where('name', 'like', "%{$s}%"));
            });
        }

        // Statistik Dashboard
        $statsQuery = clone $query;
        $totalEvents = $statsQuery->count();
        $totalDowntime = $statsQuery->sum('downtime_minutes');

        // Mesin paling sering rusak (Top 1)
        $topMachine = BreakdownReport::select('machine_id', \DB::raw('count(*) as total'))
            ->whereDate('breakdown_time', '>=', $startDate)
            ->groupBy('machine_id')
            ->orderByDesc('total')
            ->with('machine')
            ->first();

        $reports = $query->orderBy('status', 'asc') // Open di atas
            ->orderBy('breakdown_time', 'desc')
            ->paginate(10)
            ->withQueryString();

        $machines = Machine::where('status', 'active')->orderBy('name')->get();

        return view('engineering.breakdown.index', compact(
            'reports',
            'machines',
            'startDate',
            'endDate',
            'totalEvents',
            'totalDowntime',
            'topMachine'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'breakdown_time' => 'required|date',
            'category' => 'required',
            'problem_description' => 'required'
        ]);

        // Generate Code: BR-YYMM-XXX
        $prefix = 'BR-' . date('ym');
        $count = BreakdownReport::where('report_code', 'like', "$prefix%")->count() + 1;
        $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        BreakdownReport::create([
            'report_code' => $code,
            'machine_id' => $request->machine_id,
            'breakdown_time' => $request->breakdown_time,
            'category' => $request->category,
            'problem_description' => $request->problem_description,
            'status' => 'open'
        ]);

        return redirect()->back()->with('success', 'Breakdown tercatat! Segera perbaiki.');
    }

    public function update(Request $request, $id)
    {
        $report = BreakdownReport::findOrFail($id);

        $request->validate([
            'resolution_time' => 'required|date',
            'action_taken' => 'required',
            'technician' => 'required'
        ]);

        // Hitung Durasi Downtime (Menit)
        $start = Carbon::parse($report->breakdown_time);
        $end = Carbon::parse($request->resolution_time);

        // Validasi: Waktu selesai tidak boleh sebelum waktu mulai
        if ($end->lt($start)) {
            return redirect()->back()->with('error', 'Waktu selesai tidak boleh lebih awal dari waktu mulai!');
        }

        $duration = $end->diffInMinutes($start);

        $report->update([
            'resolution_time' => $request->resolution_time,
            'action_taken' => $request->action_taken,
            'technician' => $request->technician,
            'downtime_minutes' => $duration,
            'status' => 'resolved'
        ]);

        return redirect()->back()->with('success', 'Masalah terselesaikan. Downtime tercatat: ' . $duration . ' menit.');
    }
}
