<?php

namespace App\Http\Controllers;

use App\Models\IqcInspection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IqcController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter & Search
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = IqcInspection::query()
            ->whereDate('inspection_date', '>=', $startDate)
            ->whereDate('inspection_date', '<=', $endDate);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('material_name', 'like', "%{$s}%")
                    ->orWhere('supplier_name', 'like', "%{$s}%")
                    ->orWhere('batch_no', 'like', "%{$s}%");
            });
        }

        // 2. Statistik Header
        $statsQuery = clone $query; // Clone untuk hitung statistik tanpa limit pagination
        $totalInspections = $statsQuery->count();
        $totalRejected = $statsQuery->where('status', 'rejected')->count();
        $passRate = $totalInspections > 0
            ? (($totalInspections - $totalRejected) / $totalInspections) * 100
            : 100;

        // 3. Data Tabel
        $inspections = $query->orderBy('inspection_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('qc.iqc.index', compact(
            'inspections',
            'startDate',
            'endDate',
            'totalInspections',
            'totalRejected',
            'passRate'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inspection_date' => 'required|date',
            'material_name' => 'required|string',
            'qty_received' => 'required|numeric|min:0',
            'status' => 'required|in:approved,rejected,hold',
        ]);

        IqcInspection::create($request->all());

        return redirect()->route('iqc.index')->with('success', 'Data IQC berhasil disimpan.');
    }
}
