<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;
use App\Models\DailyReport;
use Carbon\Carbon;

// Command bawaan Laravel (Biarkan saja)
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// --- SCHEDULER AUTO LOCK H+2 ---
Schedule::call(function () {
    // 1. Update status menjadi 'verified' untuk data yang:
    //    - Statusnya BUKAN 'verified'
    //    - Tanggal produksinya <= 2 hari yang lalu dari sekarang
    $affectedRows = DailyReport::where('status', '!=', 'verified')
        ->whereDate('production_date', '<=', Carbon::now()->subDays(2))
        ->update(['status' => 'verified']);

    // 2. Catat ke Log (storage/logs/laravel.log) jika ada data yang diubah
    if ($affectedRows > 0) {
        Log::info("System Auto-Lock: Berhasil mengunci $affectedRows laporan produksi (H+2).");
    }
})->dailyAt('01:00')
  ->timezone('Asia/Jakarta') 
  ->name('daily-reports:autolock');
