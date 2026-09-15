<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillOfMaterial extends Model
{
    protected $guarded = [];

    public function raw_material()
    {
        return $this->belongsTo(RawMaterial::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
