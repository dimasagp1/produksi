<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\BreakdownReport;
use App\Models\WorkOrder;
use App\Models\PreventiveMaintenance;
use App\Models\SparepartTransaction;
use Illuminate\Http\Request;

class MachineHistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Machine::where('status', 'active');

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $machines = $query->orderBy('name')->paginate(12);

        return view('engineering.machine-history.index', compact('machines'));
    }

    public function show($id)
    {
        $machine = Machine::findOrFail($id);

        // 1. Ambil Breakdown
        $breakdowns = BreakdownReport::where('machine_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'breakdown',
                    'date' => $item->breakdown_time,
                    'title' => 'Breakdown: ' . $item->category,
                    'desc' => $item->problem_description,
                    'status' => $item->status,
                    'icon' => 'fa-triangle-exclamation',
                    'color' => 'rose'
                ];
            });

        // 2. Ambil Work Order
        $workOrders = WorkOrder::where('machine_id', $id)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'wo',
                    'date' => $item->created_at,
                    'title' => 'WO: ' . $item->wo_number,
                    'desc' => $item->issue_description,
                    'status' => $item->status,
                    'icon' => 'fa-screwdriver-wrench',
                    'color' => 'amber'
                ];
            });

        // 3. Ambil Preventive Maintenance (Hanya yang sudah selesai/dikerjakan)
        $pms = PreventiveMaintenance::where('machine_id', $id)
            ->whereNotNull('last_maintenance_date')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'pm',
                    'date' => $item->last_maintenance_date, // Pakai tanggal pengerjaan
                    'title' => 'Preventive: ' . $item->task_name,
                    'desc' => 'Rutin: ' . $item->frequency,
                    'status' => 'completed',
                    'icon' => 'fa-calendar-check',
                    'color' => 'blue'
                ];
            });

        // 4. Ambil Penggunaan Sparepart
        $parts = SparepartTransaction::where('machine_id', $id)
            ->where('type', 'out')
            ->with('sparepart')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'part',
                    'date' => $item->date,
                    'title' => 'Ganti Part: ' . $item->sparepart->name,
                    'desc' => 'Qty: ' . $item->quantity . ' ' . $item->sparepart->unit,
                    'status' => 'installed',
                    'icon' => 'fa-gears',
                    'color' => 'slate'
                ];
            });

        // Gabungkan semua event dan urutkan dari yang terbaru
        $timeline = $breakdowns->concat($workOrders)
            ->concat($pms)
            ->concat($parts)
            ->sortByDesc('date');

        // Statistik Ringkas
        $totalBreakdown = $breakdowns->count();
        $totalCostParts = $parts->sum(function ($item) {
            return 0;
        }); // Jika ada harga, bisa dihitung disini

        return view('engineering.machine-history.show', compact('machine', 'timeline', 'totalBreakdown'));
    }
}
