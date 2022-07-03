<?php

namespace BlackCMS\Menu\Providers;

use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use BlackCMS\Menu\Models\Menu as MenuModel;
use BlackCMS\Menu\Models\MenuLocation;
use BlackCMS\Menu\Models\MenuNode;
use BlackCMS\Menu\Repositories\Caches\MenuCacheDecorator;
use BlackCMS\Menu\Repositories\Caches\MenuLocationCacheDecorator;
use BlackCMS\Menu\Repositories\Caches\MenuNodeCacheDecorator;
use BlackCMS\Menu\Repositories\Eloquent\MenuLocationRepository;
use BlackCMS\Menu\Repositories\Eloquent\MenuNodeRepository;
use BlackCMS\Menu\Repositories\Eloquent\MenuRepository;
use BlackCMS\Menu\Repositories\Interfaces\MenuInterface;
use BlackCMS\Menu\Repositories\Interfaces\MenuLocationInterface;
use BlackCMS\Menu\Repositories\Interfaces\MenuNodeInterface;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Theme;

class MenuServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->setNamespace("packages/menu")->loadHelpers();
    }

    public function boot()
    {
        $this->app->bind(MenuInterface::class, function () {
            return new MenuCacheDecorator(new MenuRepository(new MenuModel()));
        });

        $this->app->bind(MenuNodeInterface::class, function () {
            return new MenuNodeCacheDecorator(
                new MenuNodeRepository(new MenuNode())
            );
        });

        $this->app->bind(MenuLocationInterface::class, function () {
            return new MenuLocationCacheDecorator(
                new MenuLocationRepository(new MenuLocation())
            );
        });

        $this->loadAndPublishConfigurations(["permissions", "general"])
            ->loadRoutes(["web"])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations()
            ->publishAssets();

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                "id" => "cms-core-menu",
                "priority" => 2,
                "parent_id" => "cms-core-appearance",
                "name" => "packages/menu::menu.name",
                "icon" => null,
                "url" => route("menus.index"),
                "permissions" => ["menus.index"],
            ]);

            if (!defined("THEME_MODULE_NAME")) {
                dashboard_menu()->registerItem([
                    "id" => "cms-core-appearance",
                    "priority" => 450,
                    "parent_id" => null,
                    "name" => "packages/theme::theme.appearance",
                    "icon" => "las la-brush la-2x",
                    "url" => "#",
                    "permissions" => [],
                ]);
            }

            if (function_exists("admin_bar")) {
                Theme::composer("*", function () {
                    if (Auth::check() &&
                        Auth::user()->hasPermission("menus.index")
                    ) {
                        admin_bar()->registerLink(
                            trans("packages/menu::menu.name"),
                            route("menus.index"),
                            "appearance"
                        );
                    }
                });
            }
        });

        $this->app->register(EventServiceProvider::class);
        $this->app->register(CommandServiceProvider::class);
    }
}