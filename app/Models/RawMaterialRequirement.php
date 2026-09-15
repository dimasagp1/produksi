<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_plan_id',
        'raw_material_id',
        'gross_requirement',
        'current_stock',
        'net_requirement',
        'unit'
    ];

    // Relasi ke Production Plan
    public function production_plan()
    {
        return $this->belongsTo(ProductionPlan::class);
    }

    // Relasi ke Raw Material (Bahan Baku)
    public function raw_material()
    {
        return $this->belongsTo(RawMaterial::class);
    }
}
