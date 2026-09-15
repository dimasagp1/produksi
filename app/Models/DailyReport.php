<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// 1. Import Class Spatie Activitylog
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class DailyReport extends Model
{
    // 2. Tambahkan Trait LogsActivity
    use HasFactory, LogsActivity;

    protected $guarded = ['id'];

    protected $fillable = [
        'report_code',
        'production_date',
        'batch_id',
        'machine_id',
        'shift_id',
        'coordinator_id',
        'operator_id',
        'product_id',
        'color_id',
        'packaging_type_id',
        'cycle_time',
        'cavity',
        'actual_cycle_time',
        'actual_cavity',
        'start_time',
        'end_time',
        'total_minutes',
        'qty_theory',
        'qty_actual',
        'qty_good',
        'qty_reject_total',
        'qty_sample',
        'total_runner',
        'purging_kg',
        'weight_per_pcs',
        'qty_purging',
        'total_output',
        'total_counter',
        'wip_previous',
        'wip',
        'packaging_qty',
        'downtime_total',
        'efficiency',
        'yield',
        'notes',
        'status',
    ];

    // 3. Konfigurasi Logging (Mencatat semua perubahan)
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Mencatat semua kolom di $fillable
            ->logOnlyDirty() // Hanya mencatat jika ada data yang berubah
            ->setDescriptionForEvent(fn(string $eventName) => "Daily Report has been {$eventName}");
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class);
    }

    public function operator()
    {
        return $this->belongsTo(Operator::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function packagingType()
    {
        return $this->belongsTo(PackagingType::class);
    }

    public function rejects()
    {
        return $this->hasMany(DailyReportReject::class, 'daily_report_id');
    }

    public function downtimes()
    {
        return $this->hasMany(DailyReportDowntime::class, 'daily_report_id');
    }

    public function previousReport()
    {
        return DailyReport::where('batch_id', $this->batch_id)
            ->where('machine_id', $this->machine_id)
            ->where(function ($q) {
                $q->where('production_date', '<', $this->production_date)
                    ->orWhere(function ($subQ) {
                        $subQ->where('production_date', $this->production_date)
                            ->where('created_at', '<', $this->created_at);
                    });
            })
            ->orderBy('production_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
