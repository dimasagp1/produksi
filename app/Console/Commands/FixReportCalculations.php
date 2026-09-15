<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DailyReport;
use Carbon\Carbon;

class FixReportCalculations extends Command
{
    // Nama perintah yang akan dijalankan di terminal
    protected $signature = 'fix:reports';

    // Deskripsi perintah
    protected $description = 'Menghitung ulang Qty Theory dan Efisiensi pada semua laporan harian lama.';

    public function handle()
    {
        $this->info('Memulai proses kalkulasi ulang laporan...');

        // Ambil semua data laporan
        $reports = DailyReport::all();
        $bar = $this->output->createProgressBar(count($reports));

        $bar->start();

        foreach ($reports as $report) {
            // 1. Hitung Total Durasi (Total Minutes)
            $start = Carbon::parse($report->start_time);
            $end = Carbon::parse($report->end_time);
            if ($end->lt($start)) {
                $end->addDay(); // Jika shift melewati tengah malam
            }
            $tm = $start->diffInMinutes($end);

            // 2. Ambil Downtime
            $td = $report->downtime_total ?? 0;

            // Waktu Operasional Bersih (Net Operating Time)
            $netTime = $tm - $td;

            // 3. Ambil Cycle Time dan Cavity (Prioritaskan Standar, jika 0 pakai Aktual)
            $ctStd = $report->master_cycle_time ?? $report->cycle_time ?? 0;
            $cavStd = $report->master_cavity ?? $report->cavity ?? 1;
            $ctAct = $report->actual_cycle_time ?? 0;
            $cavAct = $report->actual_cavity ?? 0;

            $ctCalc = $ctStd > 0 ? $ctStd : ($ctAct > 0 ? $ctAct : 0);
            $cavCalc = $cavStd > 0 ? $cavStd : ($cavAct > 0 ? $cavAct : 0);

            // 4. Hitung Ulang Qty Theory (Target)
            $qtyTheory = 0;
            if ($ctCalc > 0 && $netTime > 0) {
                $totalSeconds = $netTime * 60;
                $qtyTheory = floor(($totalSeconds / $ctCalc) * $cavCalc);
            }

            // 5. Hitung Ulang Efisiensi
            $qtyGood = $report->qty_good ?? 0;
            $efficiency = $qtyTheory > 0 ? ($qtyGood / $qtyTheory) * 100 : 0;

            // 6. Update langsung ke Database
            // Gunakan update() tanpa menyentuh kolom lain agar aman
            $report->update([
                'total_minutes' => $tm,
                'qty_theory'    => $qtyTheory,
                'efficiency'    => $efficiency,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Selesai! Semua data laporan berhasil diperbarui dengan rumus yang benar.');
    }
}
