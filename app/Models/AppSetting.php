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
    public static function getLogoUrl(): string
    {
        $logo = self::get('app_logo');

        if ($logo && file_exists(public_path($logo))) {
            return asset($logo);
        }

        // Fallback default image
        if (file_exists(public_path('images/logo.jpg'))) {
            return asset('images/logo.jpg');
        }

        return asset('images/logo.png');
    }

    /**
     * Get public URL for the website favicon.
     */
    public static function getFaviconUrl(): string
    {
        $favicon = self::get('app_favicon');

        if ($favicon && file_exists(public_path($favicon))) {
            return asset($favicon);
        }

        // Fallback default favicon
        if (file_exists(public_path('images/favicon.jpg'))) {
            return asset('images/favicon.jpg');
        }

        return asset('favicon.ico');
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

        if (file_exists(public_path('images/logo.png'))) {
            return public_path('images/logo.png');
        }

        return public_path('images/logo.jpg');
    }
}
