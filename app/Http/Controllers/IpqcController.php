<?php

namespace App\Http\Controllers;

use App\Models\IpqcInspection;
use App\Models\Machine;
use App\Models\Product;
use App\Models\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class IpqcController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));

        // 2. Query Data
        $query = IpqcInspection::with(['machine', 'product', 'shift'])
            ->whereDate('inspection_date', $date);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->whereHas('machine', fn($m) => $m->where('name', 'like', "%{$s}%"))
                    ->orWhereHas('product', fn($p) => $p->where('name', 'like', "%{$s}%"))
                    ->orWhere('inspector', 'like', "%{$s}%");
            });
        }

        // 3. Statistik Harian
        $statsQuery = clone $query;
        $totalCheck = $statsQuery->count();
        $totalNG = $statsQuery->where('status', 'ng')->count();
        $totalOK = $totalCheck - $totalNG;

        // 4. Data Tabel
        $inspections = $query->orderBy('inspection_time', 'desc')->paginate(10)->withQueryString();

        // 5. Data untuk Dropdown Modal
        $machines = Machine::where('status', 'active')->orderBy('name')->get();
        $products = Product::orderBy('name')->get();
        $shifts = Shift::all();

        return view('qc.ipqc.index', compact(
            'inspections',
            'date',
            'totalCheck',
            'totalOK',
            'totalNG',
            'machines',
            'products',
            'shifts'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inspection_date' => 'required|date',
            'inspection_time' => 'required',
            'machine_id' => 'required|exists:machines,id',
            'product_id' => 'required|exists:products,id',
            'status' => 'required|in:ok,ng',
        ]);

        IpqcInspection::create($request->all());

        return redirect()->route('ipqc.index', ['date' => $request->inspection_date])
            ->with('success', 'Data IPQC berhasil disimpan.');
    }
}
