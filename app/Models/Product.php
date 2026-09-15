<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'packaging_type_id',
        'packaging_qty',
        'weight'
    ];

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_product')

            ->withPivot('cycle_time', 'actual_cycle_time', 'cavity', 'actual_cavity')
            ->withTimestamps();
    }

    public function packagingType()
    {
        return $this->belongsTo(PackagingType::class);
    }

    public function bom_items()
    {
        return $this->hasMany(BillOfMaterial::class);
    }

    public function routings()
    {
        // Mengurutkan berdasarkan step_number secara otomatis
        return $this->hasMany(ProductRouting::class)->orderBy('step_number', 'asc');
    }
}
