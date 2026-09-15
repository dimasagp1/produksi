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
            'app_logo' => 'nullable|file|mimes:jpeg,png,jpg,webp,svg|max:5120',
            'app_favicon' => 'nullable|file|max:2048',
        ], [
            'app_name.required' => 'Nama website / aplikasi wajib diisi.',
            'app_logo.mimes' => 'File logo harus berupa gambar yang valid (PNG, JPG, SVG, WEBP).',
            'app_logo.max' => 'Ukuran file logo maksimal 5MB.',
            'app_favicon.max' => 'Ukuran file favicon maksimal 2MB.',
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

        $uploadDirs = [
            public_path('uploads/settings'),
        ];
        if (is_dir(base_path('public_html'))) {
            $uploadDirs[] = base_path('public_html/uploads/settings');
        }

        foreach ($uploadDirs as $dir) {
            if (!File::isDirectory($dir)) {
                try {
                    File::makeDirectory($dir, 0775, true, true);
                } catch (\Throwable $e) {
                    // Silently continue
                }
            }
        }

        $mainUploadDir = public_path('uploads/settings');

        // Handle upload logo
        if ($request->hasFile('app_logo')) {
            $oldLogo = AppSetting::get('app_logo');
            if ($oldLogo) {
                @File::delete(public_path($oldLogo));
                if (is_dir(base_path('public_html'))) {
                    @File::delete(base_path('public_html/' . $oldLogo));
                }
            }

            $file = $request->file('app_logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($mainUploadDir, $filename);

            $savedLogoPath = $mainUploadDir . '/' . $filename;

            // Sync to public_html if exists and distinct
            if (is_dir(base_path('public_html')) && realpath($mainUploadDir) !== realpath(base_path('public_html/uploads/settings'))) {
                @File::copy($savedLogoPath, base_path('public_html/uploads/settings/' . $filename));
            }

            AppSetting::set('app_logo', 'uploads/settings/' . $filename, 'image', 'appearance');

            // Synchronize logo to physical PWA icons for instant installation update
            AppSetting::syncPwaIcons($savedLogoPath);
        }

        // Handle upload favicon
        if ($request->hasFile('app_favicon')) {
            $oldFavicon = AppSetting::get('app_favicon');
            if ($oldFavicon) {
                @File::delete(public_path($oldFavicon));
                if (is_dir(base_path('public_html'))) {
                    @File::delete(base_path('public_html/' . $oldFavicon));
                }
            }

            $file = $request->file('app_favicon');
            $filename = 'favicon_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($mainUploadDir, $filename);

            $savedFaviconPath = $mainUploadDir . '/' . $filename;

            // Sync to public_html if exists and distinct
            if (is_dir(base_path('public_html')) && realpath($mainUploadDir) !== realpath(base_path('public_html/uploads/settings'))) {
                @File::copy($savedFaviconPath, base_path('public_html/uploads/settings/' . $filename));
            }

            AppSetting::set('app_favicon', 'uploads/settings/' . $filename, 'image', 'appearance');

            // If no custom logo has been uploaded, sync favicon to PWA icons
            if (empty(AppSetting::get('app_logo'))) {
                AppSetting::syncPwaIcons($savedFaviconPath);
            }
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
        if ($oldLogo) {
            @File::delete(public_path($oldLogo));
            if (is_dir(base_path('public_html'))) {
                @File::delete(base_path('public_html/' . $oldLogo));
            }
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
        if ($oldFavicon) {
            @File::delete(public_path($oldFavicon));
            if (is_dir(base_path('public_html'))) {
                @File::delete(base_path('public_html/' . $oldFavicon));
            }
        }

        AppSetting::set('app_favicon', null, 'image', 'appearance');
        AppSetting::clearCache();

        return redirect()->route('settings.app.index')->with('success', 'Favicon berhasil direset ke default sistem.');
    }

    /**
     * Dynamically serve crisp PWA icon of requested size (192 or 512).
     */
    public function pwaIcon($size)
    {
        $targetSize = (int) $size;
        if (!in_array($targetSize, [192, 512])) {
            $targetSize = 192;
        }

        $pngData = AppSetting::generatePwaIcon($targetSize);

        if ($pngData) {
            return response($pngData, 200, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-cache, private, must-revalidate',
            ]);
        }

        // Fallback to static logo
        $fallback = public_path('images/logo.png');
        if (file_exists($fallback)) {
            return response()->file($fallback, [
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-cache, private, must-revalidate',
            ]);
        }

        abort(404);
    }
}
