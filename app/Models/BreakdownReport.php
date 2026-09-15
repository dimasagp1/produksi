<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakdownReport extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'breakdown_time' => 'datetime',
        'resolution_time' => 'datetime',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
