<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequirement extends Model
{
    protected $guarded = [];

    public function raw_material()
    {
        return $this->belongsTo(RawMaterial::class);
    }
    public function production_plan()
    {
        return $this->belongsTo(ProductionPlan::class);
    }
}
