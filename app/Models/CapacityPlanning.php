<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapacityPlanning extends Model
{
    protected $guarded = [];

    public function work_center()
    {
        return $this->belongsTo(WorkCenter::class);
    }
    public function production_plan()
    {
        return $this->belongsTo(ProductionPlan::class);
    }
}
