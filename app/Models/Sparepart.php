<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sparepart extends Model
{
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(SparepartTransaction::class);
    }
}
