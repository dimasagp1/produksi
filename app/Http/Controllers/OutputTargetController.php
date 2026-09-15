<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutputTargetController extends Controller
{
    public function index(Request $request)
    {
        // 1. Query Dasar
        $query = Batch::with(['product', 'machine', 'color']);

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('batch_code', 'LIKE', "%{$search}%")
                    ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('machine', fn($m) => $m->where('name', 'LIKE', "%{$search}%"));
            });
        }

        // 3. Filter Status (Default: Running & Completed)
        if ($request->filled('filter_status')) {
            $query->where('status', $request->filter_status);
        } else {
            $query->whereIn('status', ['running', 'completed']);
        }

        $batches = $query->orderByRaw("FIELD(status, 'running', 'completed', 'planning')")
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        // 4. Statistik Global (Header)
        $totalTarget = Batch::whereIn('status', ['running'])->sum('target_quantity');
        $totalCurrent = Batch::whereIn('status', ['running'])->sum('current_quantity');

        $globalPercentage = $totalTarget > 0 ? ($totalCurrent / $totalTarget) * 100 : 0;

        // Batasi max 100% untuk visual progress bar global (agar tidak overflow)
        $globalProgressWidth = $globalPercentage > 100 ? 100 : $globalPercentage;

        return view('production.output-target.index', compact(
            'batches',
            'totalTarget',
            'totalCurrent',
            'globalPercentage',
            'globalProgressWidth'
        ));
    }
}
