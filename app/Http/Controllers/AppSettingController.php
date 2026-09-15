<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AppSettingController extends Controller
{
    /**
     * Ensure only super_admin can access.
     */
    private function checkSuperAdmin()
    {
        if (!auth()->check() || auth()->user()->role !== 'super_admin') {
            abort(403, 'Akses ditolak. Halaman ini hanya untuk Super Admin.');
        }
    }

    /**
     * Show the website settings page.
     */
    public function index()
    {
        $this->checkSuperAdmin();

        $settings = AppSetting::getAllSettings();

        return view('settings.app.index', compact('settings'));
    }

    /**
     * Update website settings.
     */
    public function update(Request $request)
    {
        $this->checkSuperAdmin();

        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_short_name' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'app_tagline' => 'nullable|string|max:255',
            'app_sub_tagline' => 'nullable|string|max:255',
            'app_description' => 'nullable|string|max:1000',
            'footer_text' => 'nullable|string|max:255',
            'footer_location' => 'nullable|string|max:100',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'app_favicon' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg,ico|max:1024',
        ], [
            'app_name.required' => 'Nama website / aplikasi wajib diisi.',
            'app_logo.image' => 'File logo harus berupa gambar yang valid (PNG, JPG, SVG, WEBP).',
            'app_logo.max' => 'Ukuran file logo maksimal 2MB.',
            'app_favicon.max' => 'Ukuran file favicon maksimal 1MB.',
        ]);

        // Simpan text field
        $textFields = [
            'app_name',
            'app_short_name',
            'company_name',
            'app_tagline',
            'app_sub_tagline',
            'app_description',
            'footer_text',
            'footer_location',
        ];

        foreach ($textFields as $field) {
            if ($request->has($field)) {
                AppSetting::set($field, $request->input($field));
            }
        }

        $uploadDir = public_path('uploads/settings');
        if (!File::isDirectory($uploadDir)) {
            File::makeDirectory($uploadDir, 0755, true, true);
        }

        // Handle upload logo
        if ($request->hasFile('app_logo')) {
            $oldLogo = AppSetting::get('app_logo');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }

            $file = $request->file('app_logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);

            AppSetting::set('app_logo', 'uploads/settings/' . $filename, 'image', 'appearance');
        }

        // Handle upload favicon
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = AppSetting::get('app_favicon');
            if ($oldFavicon && File::exists(public_path($oldFavicon))) {
                File::delete(public_path($oldFavicon));
            }

            $file = $request->file('app_favicon');
            $filename = 'favicon_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);

            AppSetting::set('app_favicon', 'uploads/settings/' . $filename, 'image', 'appearance');
        }

        AppSetting::clearCache();

        return redirect()->route('settings.app.index')->with('success', 'Identitas website berhasil diperbarui!');
    }

    /**
     * Reset logo back to default.
     */
    public function resetLogo()
    {
        $this->checkSuperAdmin();

        $oldLogo = AppSetting::get('app_logo');
        if ($oldLogo && File::exists(public_path($oldLogo))) {
            File::delete(public_path($oldLogo));
        }

        AppSetting::set('app_logo', null, 'image', 'appearance');
        AppSetting::clearCache();

        return redirect()->route('settings.app.index')->with('success', 'Logo berhasil direset ke default sistem.');
    }

    /**
     * Reset favicon back to default.
     */
    public function resetFavicon()
    {
        $this->checkSuperAdmin();

        $oldFavicon = AppSetting::get('app_favicon');
        if ($oldFavicon && File::exists(public_path($oldFavicon))) {
            File::delete(public_path($oldFavicon));
        }

        AppSetting::set('app_favicon', null, 'image', 'appearance');
        AppSetting::clearCache();

        return redirect()->route('settings.app.index')->with('success', 'Favicon berhasil direset ke default sistem.');
    }
}
