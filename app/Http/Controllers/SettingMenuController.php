<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuAccess;
use Illuminate\Http\Request;

class SettingMenuController extends Controller
{
    public function index()
    {
        // UBAH QUERY DI SINI:
        // Ambil Parent Menu (parent_id = 0) beserta anak-anaknya (children)
        // Urutkan berdasarkan 'order' agar sama persis dengan sidebar
        $menus = Menu::where('parent_id', 0)
            ->with(['children' => function ($q) {
                $q->orderBy('order');
            }])
            ->orderBy('order')
            ->get();

        // Role yang tersedia (Sesuaikan dengan kebutuhan)
        $roles = ['super_admin', 'admin', 'gm', 'manager', 'spv', 'leader', 'operator'];

        return view('settings.menus.index', compact('menus', 'roles'));
    }

    public function updateAccess(Request $request)
    {
        // Hapus semua akses lama (Reset)
        MenuAccess::truncate();

        if ($request->has('access')) {
            foreach ($request->access as $menuId => $roles) {
                foreach ($roles as $roleName => $value) {
                    MenuAccess::create([
                        'menu_id' => $menuId,
                        'role_name' => $roleName
                    ]);
                }
            }
        }

        return redirect()->back()->with('success', 'Hak akses menu berhasil diperbarui!');
    }
}
