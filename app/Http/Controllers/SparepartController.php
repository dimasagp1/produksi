<?php

namespace App\Http\Controllers;

use App\Models\Sparepart;
use App\Models\SparepartTransaction;
use App\Models\Machine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SparepartController extends Controller
{
    public function index(Request $request)
    {
        // Tab Stok (Master)
        $query = Sparepart::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%")
                ->orWhere('part_number', 'like', "%{$request->search}%");
        }
        $spareparts = $query->orderBy('name')->paginate(10, ['*'], 'parts_page')->withQueryString();

        // Tab Riwayat Transaksi
        $history = SparepartTransaction::with(['sparepart', 'machine'])
            ->orderBy('created_at', 'desc')
            ->paginate(10, ['*'], 'history_page')
            ->withQueryString();

        // Data Pendukung
        $machines = Machine::where('status', 'active')->orderBy('name')->get();

        // Statistik
        $totalItems = Sparepart::count();
        $lowStock = Sparepart::whereColumn('current_stock', '<=', 'min_stock')->count();
        $totalOutThisMonth = SparepartTransaction::where('type', 'out')
            ->whereMonth('date', now()->month)
            ->sum('quantity');

        return view('engineering.sparepart.index', compact(
            'spareparts',
            'history',
            'machines',
            'totalItems',
            'lowStock',
            'totalOutThisMonth'
        ));
    }

    public function store(Request $request)
    {
        // Tambah Sparepart Baru (Master)
        $request->validate([
            'part_number' => 'required|unique:spareparts,part_number',
            'name' => 'required',
            'min_stock' => 'required|numeric'
        ]);

        Sparepart::create($request->all());
        return redirect()->back()->with('success', 'Sparepart baru ditambahkan.');
    }

    public function transaction(Request $request)
    {
        // Catat Pemakaian / Restock
        $request->validate([
            'sparepart_id' => 'required',
            'type' => 'required|in:in,out',
            'quantity' => 'required|numeric|min:1',
            'date' => 'required|date',
            'pic' => 'required'
        ]);

        DB::transaction(function () use ($request) {
            $part = Sparepart::findOrFail($request->sparepart_id);

            // Cek Stok jika barang keluar
            if ($request->type == 'out' && $part->current_stock < $request->quantity) {
                throw new \Exception("Stok tidak cukup! Sisa: {$part->current_stock}");
            }

            // 1. Simpan Log Transaksi
            SparepartTransaction::create([
                'sparepart_id' => $request->sparepart_id,
                'type' => $request->type,
                'quantity' => $request->quantity,
                'date' => $request->date,
                'machine_id' => $request->machine_id, // Nullable
                'description' => $request->description,
                'pic' => $request->pic
            ]);

            // 2. Update Master Stok
            if ($request->type == 'in') {
                $part->increment('current_stock', $request->quantity);
            } else {
                $part->decrement('current_stock', $request->quantity);
            }
        });

        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }
}
