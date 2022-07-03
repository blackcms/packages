<?php

if (!function_exists("addon_path")) {
    /**
     * @param ?string $path
     * @return string
     */
    function addon_path(?string $path = null): string
    {
        return platform_path("addons" . DIRECTORY_SEPARATOR . $path);
    }
}

if (!function_exists("is_addon_active")) {
    /**
     * @param string $alias
     * @return bool
     */
    function is_addon_active(string $alias): bool
    {
        if (!in_array($alias, get_active_addons())) {
            return false;
        }

        $path = addon_path($alias);

        return File::isDirectory($path) &&
            File::exists($path . "/composer.json");
    }
}

if (!function_exists("get_active_addons")) {
    /**
     * @return array
     */
    function get_active_addons(): array
    {
        try {
            return array_unique(
                json_decode(setting("activated_addons", "[]"), true)
            );
        } catch (Exception $exception) {
            return [];
        }
    }
}
