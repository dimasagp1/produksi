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
     * Clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
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

        if ($logo) {
            if (file_exists(public_path($logo)) || file_exists(base_path('public_html/' . $logo))) {
                return asset($logo);
            }
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

        if ($favicon) {
            if (file_exists(public_path($favicon)) || file_exists(base_path('public_html/' . $favicon))) {
                return asset($favicon);
            }
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
    public static function getLogoPath(): string
    {
        $logo = self::get('app_logo');

        if ($logo && file_exists(public_path($logo))) {
            return public_path($logo);
        }

        if (file_exists(public_path('images/aej.png'))) {
            return public_path('images/aej.png');
        }

        if (file_exists(public_path('images/logo.jpg'))) {
            return public_path('images/logo.jpg');
        }

        if (file_exists(public_path('images/logo.png'))) {
            return public_path('images/logo.png');
        }

        return public_path('images/aej.png');
    }
}

