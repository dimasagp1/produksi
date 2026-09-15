<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionSchedule extends Model
{
    protected $guarded = [];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function work_center()
    {
        return $this->belongsTo(WorkCenter::class);
    }
}
