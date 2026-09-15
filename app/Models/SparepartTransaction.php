<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparepartTransaction extends Model
{
    protected $guarded = [];

    protected $casts = ['date' => 'date'];

    public function sparepart()
    {
        return $this->belongsTo(Sparepart::class);
    }
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
}
