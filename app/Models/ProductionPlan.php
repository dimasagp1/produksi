<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionPlan extends Model
{
    protected $guarded = [];
    protected $casts = ['period' => 'date'];

    public function items()
    {
        return $this->hasMany(ProductionPlanItem::class);
    }
}
