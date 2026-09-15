<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductRouting extends Model
{
    protected $guarded = [];

    public function work_center()
    {
        return $this->belongsTo(WorkCenter::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
