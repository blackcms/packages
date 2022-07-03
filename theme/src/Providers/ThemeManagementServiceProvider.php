<?php

namespace BlackCMS\Theme\Providers;

use BlackCMS\Base\Supports\Helper;
use Composer\Autoload\ClassLoader;
use File;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Theme;

class ThemeManagementServiceProvider extends ServiceProvider
{
    /**
     * @throws BindingResolutionException
     */
    public function register()
    {
        $theme = Theme::getThemeName();
        if (!empty($theme)) {
            $this->app
                ->make("translator")
                ->addJsonPath(theme_path($theme . "/lang"));
        }
    }

    public function boot()
    {
        $theme = Theme::getThemeName();

        if (!empty($theme)) {
            $themePath = theme_path($theme);

            if (File::exists($themePath . "/composer.json")) {
                $content = get_file_data($themePath . "/composer.json");
                if (!empty($content)) {
                    if (Arr::has($content, "extra.namespace")) {
                        $loader = new ClassLoader();
                        $loader->setPsr4(
                            $content["extra"]["namespace"],
                            theme_path($theme . "/src")
                        );
                        $loader->register();
                    }
                }
            }

            Helper::autoload(theme_path($theme . "/functions"));
        }
    }
}
