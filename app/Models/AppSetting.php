<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
    ];

    const CACHE_KEY = 'app_settings_all';

    /**
     * Get all settings as key => value array from cache or DB.
     */
    public static function getAllSettings(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return self::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            try {
                return self::query()->pluck('value', 'key')->toArray();
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    /**
     * Get a single setting value.
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAllSettings();
        $value = $settings[$key] ?? null;

        return ($value !== null && $value !== '') ? $value : $default;
    }

    /**
     * Set / Update a single setting value.
     */
    public static function set(string $key, $value, string $type = 'text', string $group = 'general'): self
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'type' => $type,
                'group' => $group,
            ]
        );

        self::clearCache();

        return $setting;
    }

    /**
     * Clear settings cache and regenerate manifest.json.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        try {
            self::updateManifestFile();
        } catch (\Throwable $e) {
            // Silently ignore
        }
    }

    /**
     * Get public URL for the website logo.
     */
    /**
     * Get public URL for the website logo.
     */
    public static function getLogoUrl(): string
    {
        $logo = self::get('app_logo');

        // If user set a custom logo, always return its URL directly
        if (!empty($logo)) {
            return asset($logo);
        }

        // 1. Check official transparent brand logo (aej.png)
        if (file_exists(public_path('images/aej.png')) || file_exists(base_path('public_html/images/aej.png'))) {
            return asset('images/aej.png');
        }

        // 2. Check logo.png
        if (file_exists(public_path('images/logo.png')) || file_exists(base_path('public_html/images/logo.png'))) {
            return asset('images/logo.png');
        }

        // 3. Check logo.jpg
        if (file_exists(public_path('images/logo.jpg')) || file_exists(base_path('public_html/images/logo.jpg'))) {
            return asset('images/logo.jpg');
        }

        // Ultimate fallback
        return asset('images/aej.png');
    }

    /**
     * Get public URL for the website favicon.
     */
    public static function getFaviconUrl(): string
    {
        $favicon = self::get('app_favicon');

        // If user set a custom favicon, always return its URL directly
        if (!empty($favicon)) {
            return asset($favicon);
        }

        // Fallback default favicon
        if (file_exists(public_path('images/favicon.jpg')) || file_exists(base_path('public_html/images/favicon.jpg'))) {
            return asset('images/favicon.jpg');
        }

        if (file_exists(public_path('images/aej.png')) || file_exists(base_path('public_html/images/aej.png'))) {
            return asset('images/aej.png');
        }

        return asset('images/favicon.jpg');
    }

    /**
     * Get absolute local path for logo (useful for DomPDF and exports).
     */
    /**
     * Get absolute local path for logo (useful for DomPDF and exports).
     */
    public static function getLogoPath(): string
    {
        $logo = self::get('app_logo');

        if ($logo) {
            if (file_exists(public_path($logo))) {
                return public_path($logo);
            }
            if (is_dir(base_path('public_html')) && file_exists(base_path('public_html/' . $logo))) {
                return base_path('public_html/' . $logo);
            }
        }

        if (file_exists(public_path('images/logo.png'))) {
            return public_path('images/logo.png');
        }

        if (file_exists(public_path('images/logo.jpg'))) {
            return public_path('images/logo.jpg');
        }

        if (file_exists(public_path('images/aej.png'))) {
            return public_path('images/aej.png');
        }

        return public_path('images/logo.png');
    }

    /**
     * Get active footer copyright text.
     */
    public static function getFooterText(): string
    {
        $footerText = self::get('footer_text');
        $companyName = self::get('company_name');
        $appName = self::get('app_name', 'HBT Produksi');

        // If user customized footer_text and it is not the legacy AEJ string
        if (!empty($footerText) && !str_contains($footerText, 'Abhimata Emas Juara')) {
            return $footerText;
        }

        // If company_name is customized (e.g. PT Herbatech Innopharma)
        if (!empty($companyName) && !str_contains($companyName, 'Abhimata Emas Juara')) {
            return $companyName . '. All rights reserved.';
        }

        if (!empty($footerText)) {
            return $footerText;
        }

        return ($companyName ?: $appName) . '. All rights reserved.';
    }

    /**
     * Generate an exact PNG icon of requested dimensions using PHP GD.
     */
    public static function generatePwaIcon(int $targetSize): ?string
    {
        $sourcePath = self::getLogoPath();
        if (!file_exists($sourcePath)) {
            $favicon = self::get('app_favicon');
            if ($favicon && file_exists(public_path($favicon))) {
                $sourcePath = public_path($favicon);
            } elseif ($favicon && is_dir(base_path('public_html')) && file_exists(base_path('public_html/' . $favicon))) {
                $sourcePath = base_path('public_html/' . $favicon);
            }
        }

        if (!file_exists($sourcePath)) {
            return null;
        }

        if (!extension_loaded('gd')) {
            return @file_get_contents($sourcePath) ?: null;
        }

        $info = @getimagesize($sourcePath);
        if (!$info) {
            return @file_get_contents($sourcePath) ?: null;
        }

        $srcW = $info[0];
        $srcH = $info[1];
        $mime = $info['mime'] ?? '';

        switch ($mime) {
            case 'image/png':
                $srcImage = @imagecreatefrompng($sourcePath);
                break;
            case 'image/jpeg':
                $srcImage = @imagecreatefromjpeg($sourcePath);
                break;
            case 'image/webp':
                $srcImage = function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null;
                break;
            default:
                $content = @file_get_contents($sourcePath);
                $srcImage = $content ? @imagecreatefromstring($content) : null;
                break;
        }

        if (!$srcImage) {
            return @file_get_contents($sourcePath) ?: null;
        }

        $dst = imagecreatetruecolor($targetSize, $targetSize);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
        $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
        imagefilledrectangle($dst, 0, 0, $targetSize, $targetSize, $transparent);

        // Maintain aspect ratio and center within canvas
        $scale = min($targetSize / $srcW, $targetSize / $srcH);
        $newW = (int) round($srcW * $scale);
        $newH = (int) round($srcH * $scale);
        $dstX = (int) round(($targetSize - $newW) / 2);
        $dstY = (int) round(($targetSize - $newH) / 2);

        imagealphablending($dst, true);
        imagecopyresampled($dst, $srcImage, $dstX, $dstY, 0, 0, $newW, $newH, $srcW, $srcH);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        ob_start();
        imagepng($dst, null, 8);
        $pngData = ob_get_clean();

        imagedestroy($dst);
        imagedestroy($srcImage);

        return $pngData;
    }

    /**
     * Generate / update manifest.json for PWA installation.
     */
    public static function updateManifestFile(): void
    {
        $appName = self::get('app_name', 'HBT Produksi');
        $shortName = self::get('app_short_name', $appName);
        $version = md5($appName . self::getLogoUrl());

        $icon192 = url('/pwa-icon/192.png') . '?v=' . $version;
        $icon512 = url('/pwa-icon/512.png') . '?v=' . $version;

        $icons = [
            [
                'src' => $icon192,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src' => $icon192,
                'sizes' => '192x192',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ],
            [
                'src' => $icon512,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'any',
            ],
            [
                'src' => $icon512,
                'sizes' => '512x512',
                'type' => 'image/png',
                'purpose' => 'maskable',
            ],
        ];

        $data = [
            'name' => $appName,
            'short_name' => $shortName,
            'start_url' => '/',
            'display' => 'standalone',
            'background_color' => '#0f172a',
            'theme_color' => '#4f46e5',
            'orientation' => 'portrait-primary',
            'icons' => $icons,
        ];

        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        $paths = [
            public_path('manifest.json'),
        ];
        if (is_dir(base_path('public_html'))) {
            $paths[] = base_path('public_html/manifest.json');
        }

        foreach ($paths as $path) {
            try {
                @file_put_contents($path, $json);
            } catch (\Throwable $e) {
                // Silently ignore
            }
        }

        self::syncPwaIcons();
    }

    /**
     * Synchronize generated PWA icons to static icon files.
     */
    public static function syncPwaIcons(string $sourcePath = ''): void
    {
        $png192 = self::generatePwaIcon(192);
        $png512 = self::generatePwaIcon(512);

        $dirs = [public_path('images')];
        if (is_dir(base_path('public_html/images'))) {
            $dirs[] = base_path('public_html/images');
        }

        foreach ($dirs as $dir) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            if ($png192) {
                @file_put_contents($dir . '/favicon192.png', $png192);
                @file_put_contents($dir . '/logo.png', $png192);
                @file_put_contents($dir . '/aej.png', $png192);
            }
            if ($png512) {
                @file_put_contents($dir . '/favicon512.png', $png512);
            }
        }
    }
}


