<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IqcInspection extends Model
{
    use HasFactory;

    protected $table = 'iqc_inspections';

    protected $fillable = [
        'inspection_date',
        'material_name',
        'supplier_name',
        'batch_no',
        'qty_received',
        'qty_rejected',
        'status',
        'remarks',
        'inspector'
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];
}
