<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OqcInspection extends Model
{
    use HasFactory;

    protected $table = 'oqc_inspections';

    protected $fillable = [
        'inspection_date',
        'inspection_time',
        'batch_id',
        'product_id',
        'sample_size',
        'defect_qty',
        'packaging_status',
        'labeling_status',
        'status',
        'remarks',
        'inspector'
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
