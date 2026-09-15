<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coa extends Model
{
    protected $guarded = [];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function items()
    {
        return $this->hasMany(CoaItem::class);
    }
}
