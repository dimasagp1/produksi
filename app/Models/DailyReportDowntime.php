<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportDowntime extends Model
{
    use HasFactory;

    protected $table = 'daily_report_downtimes'; // Pastikan nama tabel benar
    protected $guarded = ['id'];

    // Relasi balik ke Laporan Utama
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    // PENTING: Relasi ke Master Downtime
    // Controller memanggil 'downtimes.downtime', jadi nama fungsi ini WAJIB 'downtime'
    public function downtime()
    {
        return $this->belongsTo(Downtime::class, 'downtime_id');
    }
}
