<?php

namespace App\Http\Controllers;

use App\Models\DailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StandardActualController extends Controller
{
    public function index(Request $request)
    {
        // 1. Filter Tanggal
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Helper function untuk filter
        $applyFilter = function ($query) use ($request, $startDate, $endDate) {
            if ($startDate && $endDate) {
                $query->whereDate('daily_reports.production_date', '>=', $startDate)
                    ->whereDate('daily_reports.production_date', '<=', $endDate);
            }

            // Pencarian
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->whereHas('machine', fn($m) => $m->where('name', 'LIKE', "%{$search}%"))
                        ->orWhereHas('product', fn($p) => $p->where('name', 'LIKE', "%{$search}%"))
                        ->orWhereHas('batch', fn($b) => $b->where('batch_code', 'LIKE', "%{$search}%"));
                });
            }
        };

        // 2. Query Utama
        $query = DailyReport::with(['machine', 'product', 'batch', 'shift'])
            ->leftJoin('machine_product', function ($join) {
                $join->on('daily_reports.machine_id', '=', 'machine_product.machine_id')
                    ->on('daily_reports.product_id', '=', 'machine_product.product_id');
            })
            ->select(
                'daily_reports.*',

                // Ambil Cycle Time Standar (Prioritas: Master Spesifik -> Master Umum -> 0)
                DB::raw('COALESCE(
                    machine_product.cycle_time, 
                    (SELECT cycle_time FROM machine_product WHERE product_id = daily_reports.product_id AND cycle_time > 0 LIMIT 1),
                    0
                ) as master_cycle_time'),

                // Ambil Cavity Standar (Prioritas: Master Spesifik -> Master Umum -> 0)
                DB::raw('COALESCE(
                    machine_product.cavity, 
                    (SELECT cavity FROM machine_product WHERE product_id = daily_reports.product_id AND cavity > 0 LIMIT 1),
                    0
                ) as master_cavity')
            );

        $applyFilter($query);

        // PERBAIKAN 1: Sorting disamakan dengan Daily Report (Tanggal DESC, lalu Waktu Input DESC)
        $reports = $query->orderBy('daily_reports.production_date', 'desc')
            ->orderBy('daily_reports.created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // 3. Ringkasan Statistik
        $statsQuery = DailyReport::query()
            ->leftJoin('machine_product', function ($join) {
                $join->on('daily_reports.machine_id', '=', 'machine_product.machine_id')
                    ->on('daily_reports.product_id', '=', 'machine_product.product_id');
            })
            ->select(
                'daily_reports.*',
                DB::raw('COALESCE(
                    machine_product.cycle_time, 
                    (SELECT cycle_time FROM machine_product WHERE product_id = daily_reports.product_id AND cycle_time > 0 LIMIT 1),
                    0
                ) as master_cycle_time'),
                DB::raw('COALESCE(
                    machine_product.cavity, 
                    (SELECT cavity FROM machine_product WHERE product_id = daily_reports.product_id AND cavity > 0 LIMIT 1),
                    0
                ) as master_cavity')
            );

        $applyFilter($statsQuery);

        $allData = $statsQuery->get();

        $totalRecords = $allData->count();

        $ctProblemCount = $allData->filter(function ($r) {
            // PERBAIKAN 2: Hanya bandingkan jika Master > 0
            if ($r->master_cycle_time <= 0) return false;
            return (float)$r->actual_cycle_time > (float)$r->master_cycle_time;
        })->count();

        $cavityProblemCount = $allData->filter(function ($r) {
            // PERBAIKAN 2: Hanya bandingkan jika Master > 0
            if ($r->master_cavity <= 0) return false;
            return (int)$r->actual_cavity < (int)$r->master_cavity;
        })->count();

        return view('production.standard-actual.index', compact(
            'reports',
            'startDate',
            'endDate',
            'totalRecords',
            'ctProblemCount',
            'cavityProblemCount'
        ));
    }
}
