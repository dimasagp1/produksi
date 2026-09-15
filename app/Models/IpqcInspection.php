<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IpqcInspection extends Model
{
    use HasFactory;

    protected $table = 'ipqc_inspections';

    protected $fillable = [
        'inspection_date',
        'inspection_time',
        'machine_id',
        'product_id',
        'shift_id',
        'status',
        'remarks',
        'inspector'
    ];

    protected $casts = [
        'inspection_date' => 'date',
    ];

    // Relasi ke Master Data
    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
