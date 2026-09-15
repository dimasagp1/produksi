<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $guarded = ['id'];

    // Relasi ke tabel akses
    public function accesses()
    {
        return $this->hasMany(MenuAccess::class, 'menu_id');
    }

    // Untuk mengecek apakah role tertentu punya akses (dipakai di Blade nanti)
    public function hasAccess($role)
    {
        return $this->accesses->where('role_name', $role)->isNotEmpty();
    }

    // Relasi untuk Submenu (Recursive)
    public function children()
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }
}
