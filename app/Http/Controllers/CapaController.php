<?php

namespace App\Http\Controllers;

use App\Models\CapaAction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CapaController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $query = CapaAction::query();

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('code', 'like', "%{$s}%")
                    ->orWhere('problem_description', 'like', "%{$s}%")
                    ->orWhere('pic', 'like', "%{$s}%");
            });
        }

        // Statistik
        $totalOpen = CapaAction::where('status', 'open')->count();
        $totalClosed = CapaAction::where('status', 'closed')->count();
        $totalOverdue = CapaAction::where('status', 'open')
            ->whereDate('due_date', '<', Carbon::today())
            ->count();

        $actions = $query->orderBy('status', 'desc') // Open dulu
            ->orderBy('issue_date', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('qc.capa.index', compact(
            'actions',
            'totalOpen',
            'totalClosed',
            'totalOverdue'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'issue_date' => 'required|date',
            'source' => 'required',
            'problem_description' => 'required',
            'pic' => 'required',
            'due_date' => 'required|date'
        ]);

        // Auto Generate Code: CPA-YYMM-XXX
        $prefix = 'CPA-' . date('ym');
        $last = CapaAction::where('code', 'like', "$prefix%")->count();
        $code = $prefix . '-' . str_pad($last + 1, 3, '0', STR_PAD_LEFT);

        CapaAction::create([
            'code' => $code,
            'issue_date' => $request->issue_date,
            'source' => $request->source,
            'problem_description' => $request->problem_description,
            'root_cause' => $request->root_cause,
            'corrective_action' => $request->corrective_action,
            'preventive_action' => $request->preventive_action,
            'pic' => $request->pic,
            'due_date' => $request->due_date,
            'status' => 'open'
        ]);

        return redirect()->route('capa.index')->with('success', 'CAPA berhasil dibuat.');
    }

    public function update(Request $request, $id)
    {
        $capa = CapaAction::findOrFail($id);

        // Simpel update untuk status/penyelesaian
        $capa->update([
            'root_cause' => $request->root_cause,
            'corrective_action' => $request->corrective_action,
            'preventive_action' => $request->preventive_action,
            'status' => $request->status ?? $capa->status
        ]);

        return redirect()->route('capa.index')->with('success', 'CAPA diperbarui.');
    }
}
