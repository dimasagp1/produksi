<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'report_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
