<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'code', 'status'];

    public function items()
    {
        return $this->hasMany(RejectItem::class, 'category_id');
    }
}