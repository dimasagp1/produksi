<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        // 1. Base Query
        $query = Activity::with('causer')->latest();

        // 2. Logic Pencarian (Search)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%$search%")
                    ->orWhereHas('causer', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%$search%");
                    });
            });
        }

        // 3. Logic Filter Berdasarkan Summary Card (Event Click)
        if ($request->has('event')) {
            $filter = $request->event;

            if ($filter === 'created') {
                $query->where('event', 'created');
            } elseif ($filter === 'updated') {
                $query->where('event', 'updated');
            } elseif ($filter === 'deleted') {
                $query->where('event', 'deleted');
            } elseif ($filter === 'users') {
                // Filter hanya aktivitas yang dilakukan oleh user (bukan sistem/bot)
                $query->whereNotNull('causer_id');
            }
        }

        // --- STATISTIK SUMMARY (ALL TIME) ---
        // Hitung terpisah agar angka di kartu TIDAK berubah saat difilter
        $totalLogs    = Activity::count();
        $totalCreated = Activity::where('event', 'created')->count();
        $totalUpdated = Activity::where('event', 'updated')->count(); // NEW
        $totalDeleted = Activity::where('event', 'deleted')->count();
        $totalUsers   = Activity::distinct('causer_id')->whereNotNull('causer_id')->count('causer_id');
        // ------------------------------------

        $logs = $query->paginate(20)->withQueryString(); // withQueryString agar filter tetap ada saat pagination

        return view('activity-logs.index', compact(
            'logs',
            'totalLogs',
            'totalCreated',
            'totalUpdated',
            'totalDeleted',
            'totalUsers'
        ));
    }
}
