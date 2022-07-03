<?php

namespace BlackCMS\Addon\Providers;

use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use Composer\Autoload\ClassLoader;
use Exception;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Psr\SimpleCache\InvalidArgumentException;

class AddonServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function boot()
    {
        $this->setNamespace("packages/addon")
            ->loadAndPublishConfigurations(["permissions"])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadRoutes(["web"])
            ->loadHelpers()
            ->publishAssets();

        $addons = get_active_addons();
        if (!empty($addons) && is_array($addons)) {
            $loader = new ClassLoader();
            $providers = [];
            $namespaces = [];
            if (cache()->has("addon_namespaces") &&
                cache()->has("addon_providers")
            ) {
                $providers = cache("addon_providers");
                if (!is_array($providers) || empty($providers)) {
                    $providers = [];
                }

                $namespaces = cache("addon_namespaces");

                if (!is_array($namespaces) || empty($namespaces)) {
                    $namespaces = [];
                }
            }

            if (empty($namespaces) || empty($providers)) {
                foreach ($addons as $addon) {
                    if (empty($addon)) {
                        continue;
                    }

                    $addonPath = addon_path($addon);

                    if (!File::exists($addonPath . "/composer.json")) {
                        continue;
                    }
                    $content = get_file_data($addonPath . "/composer.json");
                    if (!empty($content)) {
                        if (Arr::has($content, "extra.namespace") &&
                            !class_exists($content["extra"]["provider"])
                        ) {
                            $namespaces[$addon] =
                                $content["extra"]["namespace"];
                        }

                        $providers[] = $content["extra"]["provider"];
                    }
                }

                cache()->forever("addon_namespaces", $namespaces);
                cache()->forever("addon_providers", $providers);
            }

            foreach ($namespaces as $key => $namespace) {
                $loader->setPsr4($namespace, addon_path($key . "/src"));
            }

            $loader->register();

            foreach ($providers as $provider) {
                if (!class_exists($provider)) {
                    continue;
                }

                $this->app->register($provider);
            }
        }

        $this->app->register(CommandServiceProvider::class);

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                "id" => "cms-core-addons",
                "priority" => 460,
                "parent_id" => null,
                "name" => "core/base::layouts.addons",
                "icon" => "las la-puzzle-piece la-2x",
                "url" => route("addons.index"),
                "permissions" => ["addons.index"],
            ]);
        });

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}
