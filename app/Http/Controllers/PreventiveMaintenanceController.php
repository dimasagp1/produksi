<?php

namespace App\Http\Controllers;

use App\Models\PreventiveMaintenance;
use App\Models\Machine;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PreventiveMaintenanceController extends Controller
{
    public function index(Request $request)
    {
        $query = PreventiveMaintenance::with('machine');

        // Update status Overdue otomatis jika hari ini > due_date
        PreventiveMaintenance::whereDate('next_due_date', '<', Carbon::today())
            ->update(['status' => 'overdue']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('task_name', 'like', "%{$s}%")
                ->orWhereHas('machine', fn($q) => $q->where('name', 'like', "%{$s}%"));
        }

        if ($request->filled('frequency')) {
            $query->where('frequency', $request->frequency);
        }

        $schedules = $query->orderBy('next_due_date', 'asc')->paginate(10)->withQueryString();
        $machines = Machine::where('status', 'active')->orderBy('name')->get();

        // Statistik
        $totalOverdue = PreventiveMaintenance::whereDate('next_due_date', '<', Carbon::today())->count();
        $dueThisWeek = PreventiveMaintenance::whereBetween('next_due_date', [Carbon::today(), Carbon::today()->addDays(7)])->count();

        return view('engineering.preventive.index', compact('schedules', 'machines', 'totalOverdue', 'dueThisWeek'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'task_name' => 'required',
            'frequency' => 'required',
            'next_due_date' => 'required|date',
        ]);

        PreventiveMaintenance::create($request->all());

        return redirect()->back()->with('success', 'Jadwal PM berhasil dibuat.');
    }

    public function complete($id)
    {
        $pm = PreventiveMaintenance::findOrFail($id);

        // 1. Simpan tanggal selesai hari ini
        $pm->last_maintenance_date = Carbon::today();

        // 2. Hitung Next Due Date baru berdasarkan Frekuensi
        switch ($pm->frequency) {
            case 'daily':
                $next = Carbon::today()->addDay();
                break;
            case 'weekly':
                $next = Carbon::today()->addWeek();
                break;
            case 'monthly':
                $next = Carbon::today()->addMonth();
                break;
            case 'quarterly':
                $next = Carbon::today()->addMonths(3);
                break;
            case 'yearly':
                $next = Carbon::today()->addYear();
                break;
            default:
                $next = Carbon::today()->addMonth();
        }

        $pm->next_due_date = $next;
        $pm->status = 'scheduled'; // Reset status jadi aman
        $pm->save();

        return redirect()->back()->with('success', 'Perawatan selesai! Jadwal berikutnya telah diperbarui.');
    }
}
