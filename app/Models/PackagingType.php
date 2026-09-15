<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackagingType extends Model
{
    use HasFactory;

    // Pastikan nama tabel benar (opsional jika mengikuti konvensi Laravel)
    protected $table = 'packaging_types';

    protected $fillable = [
        'name',
        'conversion_quantity',
        'content_unit',
        'status'
    ];

    // Relasi: Satu jenis kemasan bisa memiliki banyak produk
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
