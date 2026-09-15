<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DowntimeTrackingController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Tanggal dari Filter (Bisa NULL)
        $filterDate = $request->input('filter_date');

        // 2. Base Query untuk Tabel (Re-useable)
        $baseQuery = DB::table('daily_report_downtimes as drd')
            ->join('daily_reports as dr', 'drd.daily_report_id', '=', 'dr.id')
            ->join('machines as m', 'dr.machine_id', '=', 'm.id');

        // Terapkan Filter Tanggal jika ada
        if ($filterDate) {
            $baseQuery->whereDate('dr.production_date', $filterDate);
        }

        // 3. Statistik
        $stats = (clone $baseQuery)->selectRaw('
            COALESCE(SUM(drd.duration), 0) as total_duration,
            COUNT(drd.id) as total_events
        ')->first();

        // 4. Mesin Paling Sering Down
        $topMachine = (clone $baseQuery)
            ->select('m.name', DB::raw('SUM(drd.duration) as duration'))
            ->groupBy('m.name')
            ->orderByDesc('duration')
            ->first();

        // 5. Tabel Log Detail (Main Data)
        $query = DB::table('daily_report_downtimes as drd')
            ->join('daily_reports as dr', 'drd.daily_report_id', '=', 'dr.id')
            ->join('downtimes as d', 'drd.downtime_id', '=', 'd.id')
            ->join('machines as m', 'dr.machine_id', '=', 'm.id')
            ->join('products as p', 'dr.product_id', '=', 'p.id')
            ->leftJoin('shifts as s', 'dr.shift_id', '=', 's.id')
            ->leftJoin('batches as b', 'dr.batch_id', '=', 'b.id') // TAMBAHAN: Join ke tabel Batch
            ->select(
                'drd.id',
                'drd.created_at',
                'drd.duration',
                'drd.remarks',
                'dr.report_code',
                'dr.production_date',
                'b.batch_code', // TAMBAHAN: Ambil kode batch
                'm.name as machine_name',
                'p.name as product_name',
                'd.name as downtime_reason',
                's.name as shift_name'
            )
            ->orderBy('drd.created_at', 'desc');

        // Terapkan Filter Tanggal ke Query Utama
        if ($filterDate) {
            $query->whereDate('dr.production_date', $filterDate);
        }

        // Fitur Pencarian
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('m.name', 'like', "%{$s}%")
                    ->orWhere('d.name', 'like', "%{$s}%")
                    ->orWhere('p.name', 'like', "%{$s}%")
                    ->orWhere('drd.remarks', 'like', "%{$s}%")
                    ->orWhere('b.batch_code', 'like', "%{$s}%"); // Tambahan search by batch
            });
        }

        $logs = $query->paginate(10)->withQueryString();

        return view('production.downtime-tracking.index', compact(
            'stats',
            'topMachine',
            'logs',
            'filterDate'
        ));
    }
}
