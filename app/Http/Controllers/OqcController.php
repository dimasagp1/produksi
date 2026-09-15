<?php

namespace App\Http\Controllers;

use App\Models\OqcInspection;
use App\Models\Batch;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OqcController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        $query = OqcInspection::with(['batch', 'product'])
            ->whereDate('inspection_date', $date);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('product', fn($p) => $p->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('batch', fn($b) => $b->where('batch_code', 'like', "%{$s}%"))
                    ->orWhere('inspector', 'like', "%{$s}%");
            });
        }

        // Statistik Sederhana
        $statsQuery = clone $query;
        $totalCheck = $statsQuery->count();
        $totalPass = $statsQuery->where('status', 'pass')->count();
        $totalReject = $statsQuery->where('status', 'reject')->count();

        $inspections = $query->orderBy('inspection_time', 'desc')->paginate(10)->withQueryString();

        // Data untuk Modal (Ambil Batch terbaru)
        $batches = Batch::with('product')->orderBy('created_at', 'desc')->limit(50)->get();

        return view('qc.oqc.index', compact(
            'inspections',
            'date',
            'totalCheck',
            'totalPass',
            'totalReject',
            'batches'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inspection_date' => 'required|date',
            'batch_id' => 'required|exists:batches,id',
            'status' => 'required|in:pass,reject,hold',
        ]);

        // Auto fill product_id dari batch
        $batch = Batch::find($request->batch_id);

        $data = $request->all();
        $data['product_id'] = $batch->product_id;

        OqcInspection::create($data);

        return redirect()->route('oqc.index', ['date' => $request->inspection_date])
            ->with('success', 'Data OQC berhasil disimpan.');
    }
}
