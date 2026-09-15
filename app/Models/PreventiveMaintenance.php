<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreventiveMaintenance extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'last_maintenance_date' => 'date',
        'next_due_date' => 'date',
    ];

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
