<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Require global helpers
        $helpers = app_path('Helpers/helpers.php');
        if (file_exists($helpers)) {
            require_once $helpers;
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Share app settings globally with all views
        View::composer('*', function ($view) {
            $view->with('appSetting', \App\Models\AppSetting::getAllSettings());
        });

        // View Composer: Logika ini akan dijalankan setiap kali view di folder 'layouts' dipanggil.
        // Jika sidebar Anda ada di resources/views/layouts/sidebar.blade.php,
        // maka 'layouts.*' atau 'layouts.sidebar' sudah benar.

        View::composer('layouts.*', function ($view) {
            // Cek apakah user sedang login
            if (Auth::check()) {
                // Ambil role user dari session/database
                $userRole = Auth::user()->role;

                // Ambil Menu Utama (Parent = 0) yang boleh diakses role ini
                $sidebarMenus = Menu::whereHas('accesses', function ($q) use ($userRole) {
                    $q->where('role_name', $userRole);
                })
                    ->where('parent_id', 0)
                    ->orderBy('order')
                    ->with(['children' => function ($q) use ($userRole) {
                        // Eager Load Sub-menu, TAPI filter juga berdasarkan akses role
                        // Agar submenu yang dilarang tidak ikut muncul
                        $q->whereHas('accesses', function ($sq) use ($userRole) {
                            $sq->where('role_name', $userRole);
                        })->orderBy('order');
                    }])
                    ->get();

                // Inject variabel $sidebarMenus ke view
                $view->with('sidebarMenus', $sidebarMenus);
            }
        });
    }
}
