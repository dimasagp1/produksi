<?php

namespace App\Http\Controllers;

use App\Models\Coa;
use App\Models\CoaItem;
use App\Models\Batch;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CoaController extends Controller
{
    public function index(Request $request)
    {
        $query = Coa::with(['batch.product']);

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where('coa_code', 'like', "%{$s}%")
                ->orWhere('customer_name', 'like', "%{$s}%")
                ->orWhereHas('batch', fn($q) => $q->where('batch_code', 'like', "%{$s}%"));
        }

        $coas = $query->orderBy('created_at', 'desc')->paginate(10);

        // Ambil batch yang lulus QC (opsional, disini ambil semua batch terbaru)
        $batches = Batch::with('product')->orderBy('created_at', 'desc')->limit(50)->get();

        return view('qc.coa.index', compact('coas', 'batches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => 'required',
            'report_date' => 'required|date',
            'approver_name' => 'required',
            'items' => 'required|array|min:1'
        ]);

        // Generate Nomor COA: COA/THN/BLN/XXX
        $count = Coa::whereYear('created_at', date('Y'))->count() + 1;
        $coaCode = 'COA/' . date('Y/m/') . str_pad($count, 3, '0', STR_PAD_LEFT);

        $coa = Coa::create([
            'coa_code' => $coaCode,
            'batch_id' => $request->batch_id,
            'customer_name' => $request->customer_name,
            'manufacture_date' => $request->manufacture_date,
            'expiry_date' => $request->expiry_date,
            'report_date' => $request->report_date,
            'approver_name' => $request->approver_name,
            'remarks' => $request->remarks
        ]);

        // Simpan Detail Item
        foreach ($request->items as $item) {
            if (!empty($item['parameter'])) {
                $coa->items()->create($item);
            }
        }

        return redirect()->route('coa.index')->with('success', 'COA berhasil diterbitkan.');
    }

    public function show($id)
    {
        // Fungsi untuk Cetak / Print View
        $coa = Coa::with(['batch.product', 'items'])->findOrFail($id);
        return view('qc.coa.print', compact('coa'));
    }
}
