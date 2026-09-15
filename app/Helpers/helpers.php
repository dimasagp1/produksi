<?php

use App\Models\AppSetting;

if (!function_exists('app_setting')) {
    /**
     * Get an app setting value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function app_setting(string $key, $default = null)
    {
        return AppSetting::get($key, $default);
    }
}

if (!function_exists('app_logo_url')) {
    /**
     * Get the active logo URL.
     *
     * @return string
     */
    function app_logo_url(): string
    {
        return AppSetting::getLogoUrl();
    }
}

if (!function_exists('app_favicon_url')) {
    /**
     * Get the active favicon URL.
     *
     * @return string
     */
    function app_favicon_url(): string
    {
        return AppSetting::getFaviconUrl();
    }
}

if (!function_exists('app_logo_path')) {
    /**
     * Get the active logo local file path.
     *
     * @return string
     */
    function app_logo_path(): string
    {
        return AppSetting::getLogoPath();
    }
}

if (!function_exists('app_footer_text')) {
    /**
     * Get the active footer text.
     *
     * @return string
     */
    function app_footer_text(): string
    {
        return AppSetting::getFooterText();
    }
}
