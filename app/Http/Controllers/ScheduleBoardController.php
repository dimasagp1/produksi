<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\WorkCenter;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleBoardController extends Controller
{
    public function index()
    {
        $workCenters = WorkCenter::orderBy('name')->get();
        $groups = $workCenters->map(function ($wc) {
            return ['id' => $wc->id, 'content' => $wc->name];
        });

        $scheduledBatches = Batch::with(['product', 'work_center'])
            ->whereNotNull('planned_start')
            ->whereNotNull('work_center_id')
            ->get();

        $items = $scheduledBatches->map(function ($batch) {
            return [
                'id' => $batch->id,
                'group' => $batch->work_center_id,
                // Content mengandung HTML untuk Timeline Vis.js
                'content' => "<b>{$batch->batch_code}</b><br>{$batch->product->name}",
                'start' => $batch->planned_start->toIso8601String(),
                'end' => $batch->planned_end->toIso8601String(),
                'style' => "background-color: {$batch->visual_color}; border-color: {$batch->visual_color}; color: white; border-radius: 6px;",
                'title' => "Qty: " . number_format($batch->quantity),
                'batch_id' => $batch->id
            ];
        });

        $unscheduledBatches = Batch::whereNull('planned_start')
            ->where('status', '!=', 'completed')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('ppic.schedule.index', compact('groups', 'items', 'workCenters', 'unscheduledBatches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required|exists:batches,id',
            'work_center_id' => 'required|exists:work_centers,id',
            'start_time' => 'required|date',
            'duration_hours' => 'required|numeric|min:0.1',
        ]);

        $batch = Batch::findOrFail($request->batch_id);
        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours((float) $request->duration_hours);

        $batch->update([
            'work_center_id' => $request->work_center_id,
            'planned_start' => $start,
            'planned_end' => $end,
            'visual_color' => $request->visual_color ?? '#3b82f6',
            // Hapus update status jika menyebabkan error ENUM
        ]);

        return redirect()->back()->with('success', 'Jadwal Batch berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'work_center_id' => 'required|exists:work_centers,id',
            'start_time' => 'required|date',
            'duration_hours' => 'required|numeric|min:0.1',
        ]);

        $batch = Batch::findOrFail($id);
        $start = Carbon::parse($request->start_time);
        $end = $start->copy()->addHours((float) $request->duration_hours);

        $batch->update([
            'work_center_id' => $request->work_center_id,
            'planned_start' => $start,
            'planned_end' => $end,
            'visual_color' => $request->visual_color ?? $batch->visual_color
        ]);

        return redirect()->back()->with('success', 'Jadwal Batch berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $batch = Batch::findOrFail($id);

        // [PERBAIKAN UTAMA] 
        // 1. Jangan ubah status jika DB tidak support ENUM 'pending'
        // 2. visual_color TIDAK BOLEH NULL, set ke default color
        $batch->update([
            'work_center_id' => null,
            'planned_start' => null,
            'planned_end' => null,
            'visual_color' => '#3b82f6' // Reset ke warna default (Biru)
        ]);

        return redirect()->back()->with('success', 'Jadwal Batch dihapus (kembali ke Pending).');
    }
}
