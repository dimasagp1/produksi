<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    protected $fillable = [
        'name',
        'status',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('cycle_time', 'cavity')
            ->withTimestamps();
    }

    public function rejectItems()
    {
        return $this->belongsToMany(RejectItem::class, 'machine_reject_item');
    }
}
