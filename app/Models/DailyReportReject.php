<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReportReject extends Model
{
    use HasFactory;

    protected $table = 'daily_report_rejects'; // Pastikan nama tabel benar
    protected $guarded = ['id'];

    // Relasi balik ke Laporan Utama (Opsional, tapi baik ada)
    public function dailyReport()
    {
        return $this->belongsTo(DailyReport::class);
    }

    // PENTING: Relasi ke Master Reject Item
    // Controller memanggil 'rejects.rejectItem', jadi nama fungsi ini WAJIB 'rejectItem'
    public function rejectItem()
    {
        return $this->belongsTo(RejectItem::class, 'reject_item_id');
    }
}
