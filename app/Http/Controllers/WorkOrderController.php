<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use App\Models\Machine;
use Illuminate\Http\Request;
use Carbon\Carbon;

class WorkOrderController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = WorkOrder::with('machine');

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('wo_number', 'like', "%{$s}%")
                ->orWhereHas('machine', fn($q) => $q->where('name', 'like', "%{$s}%"))
                ->orWhere('assigned_to', 'like', "%{$s}%");
        }

        // Statistik
        $totalPending = WorkOrder::where('status', 'pending')->count();
        $totalProgress = WorkOrder::where('status', 'in_progress')->count();
        $totalCompleted = WorkOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $workOrders = $query->orderByRaw("FIELD(status, 'pending', 'in_progress', 'completed', 'cancelled')")
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $machines = Machine::where('status', 'active')->orderBy('name')->get();

        return view('engineering.work-order.index', compact(
            'workOrders',
            'machines',
            'totalPending',
            'totalProgress',
            'totalCompleted'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'issue_description' => 'required',
            'priority' => 'required'
        ]);

        // Generate WO Number: WO-YYMM-XXX
        $prefix = 'WO-' . date('ym');
        $count = WorkOrder::where('wo_number', 'like', "$prefix%")->count() + 1;
        $code = $prefix . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        WorkOrder::create([
            'wo_number' => $code,
            'report_date' => now(),
            'machine_id' => $request->machine_id,
            'reported_by' => auth()->user()->name ?? 'Operator', // Asumsi ada auth
            'priority' => $request->priority,
            'issue_description' => $request->issue_description,
            'status' => 'pending'
        ]);

        return redirect()->back()->with('success', 'Work Order berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $wo = WorkOrder::findOrFail($id);

        $data = [
            'assigned_to' => $request->assigned_to,
            'status' => $request->status,
            'action_taken' => $request->action_taken,
        ];

        // Auto Timestamp logic
        if ($request->status == 'in_progress' && !$wo->start_time) {
            $data['start_time'] = now();
        }
        if ($request->status == 'completed' && !$wo->end_time) {
            $data['end_time'] = now();
        }

        $wo->update($data);

        return redirect()->back()->with('success', 'Status Work Order diperbarui.');
    }
}
