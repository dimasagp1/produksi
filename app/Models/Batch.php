<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Batch extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'batch_code',
        'product_id',
        'color_id',
        'machine_id',
        'target_quantity',
        'current_quantity',
        'reject_quantity',
        'start_date',
        'deadline_date',
        'status',
        'priority',
        'notes',
        'is_active',

        // === KOLOM BARU UNTUK SCHEDULE BOARD ===
        'work_center_id', // Jika Anda memisahkan Work Center dan Machine
        'planned_start',  // Waktu Mulai (Jam & Tanggal)
        'planned_end',    // Waktu Selesai (Jam & Tanggal)
        'visual_color'    // Warna Bar di Gantt Chart
    ];

    // === CASTING TIPE DATA (PENTING) ===
    // Ini mengubah string database menjadi object Carbon secara otomatis
    protected $casts = [
        'planned_start' => 'datetime',
        'planned_end' => 'datetime',
        'start_date' => 'date',
        'deadline_date' => 'date',
        'is_active' => 'boolean',
    ];

    // ==========================================
    // SPATIE ACTIVITY LOG CONFIGURATION
    // ==========================================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Mencatat semua field yang ada di $fillable
            ->logOnlyDirty() // Hanya mencatat field yang berubah saja (saat update)
            ->dontSubmitEmptyLogs() // Jangan catat jika tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Batch {$this->batch_code} has been {$eventName}");
    }

    // Relasi ke Produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Relasi ke Warna
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    // Relasi ke Mesin (Aset Fisik)
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    // Relasi ke Work Center (Grup Kerja untuk Penjadwalan)
    // Sesuai dengan migrasi "add_scheduling_to_batches_table" sebelumnya
    public function work_center()
    {
        return $this->belongsTo(WorkCenter::class);
    }

    // Relasi ke DailyReport
    public function reports()
    {
        return $this->hasMany(DailyReport::class);
    }

    // Helper Progress
    public function getProgressAttribute()
    {
        if ($this->target_quantity == 0) return 0;
        return round(($this->current_quantity / $this->target_quantity) * 100, 1);
    }

    public function getQuantityAttribute()
    {
        return $this->target_quantity;
    }
}
