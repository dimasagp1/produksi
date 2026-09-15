<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectItem extends Model
{
    use HasFactory;
    protected $fillable = ['category_id', 'name', 'status'];

    public function category()
    {
        return $this->belongsTo(RejectCategory::class, 'category_id');
    }

    public function machines()
    {
        return $this->belongsToMany(Machine::class, 'machine_reject_item');
    }
}
