<?php

namespace BlackCMS\Widget\Providers;

use BlackCMS\Base\Traits\LoadAndPublishDataTrait;
use BlackCMS\Widget\Factories\WidgetFactory;
use BlackCMS\Widget\Misc\LaravelApplicationWrapper;
use BlackCMS\Widget\Models\Widget;
use BlackCMS\Widget\Repositories\Caches\WidgetCacheDecorator;
use BlackCMS\Widget\Repositories\Eloquent\WidgetRepository;
use BlackCMS\Widget\Repositories\Interfaces\WidgetInterface;
use BlackCMS\Widget\WidgetGroupCollection;
use BlackCMS\Widget\Widgets\Text;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use File;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\ServiceProvider;
use Theme;
use WidgetGroup;

class WidgetServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WidgetInterface::class, function () {
            return new WidgetCacheDecorator(new WidgetRepository(new Widget()));
        });

        $this->app->bind("blackcms.widget", function () {
            return new WidgetFactory(new LaravelApplicationWrapper());
        });

        $this->app->singleton("blackcms.widget-group-collection", function () {
            return new WidgetGroupCollection(new LaravelApplicationWrapper());
        });

        $this->setNamespace("packages/widget")->loadHelpers();
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadAndPublishConfigurations(["permissions"])
            ->loadRoutes(["web"])
            ->loadMigrations()
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->publishAssets();

        $this->app->booted(function () {
            WidgetGroup::setGroup([
                "id" => "primary_sidebar",
                "name" => trans("packages/widget::widget.primary_sidebar_name"),
                "description" => trans(
                    "packages/widget::widget.primary_sidebar_description"
                ),
            ]);

            register_widget(Text::class);

            $widgetPath = theme_path(Theme::getThemeName() . "/widgets");
            $widgets = scan_folder($widgetPath);
            if (!empty($widgets) && is_array($widgets)) {
                foreach ($widgets as $widget) {
                    $registration =
                        $widgetPath . "/" . $widget . "/registration.php";
                    if (File::exists($registration)) {
                        File::requireOnce($registration);
                    }
                }
            }
        });

        Event::listen(RouteMatched::class, function () {
            dashboard_menu()->registerItem([
                "id" => "cms-core-widget",
                "priority" => 3,
                "parent_id" => "cms-core-appearance",
                "name" => "packages/widget::widget.name",
                "icon" => null,
                "url" => route("widgets.index"),
                "permissions" => ["widgets.index"],
            ]);

            if (function_exists("admin_bar")) {
                Theme::composer("*", function () {
                    if (Auth::check() &&
                        Auth::user()->hasPermission("menus.index")
                    ) {
                        admin_bar()->registerLink(
                            trans("packages/widget::widget.name"),
                            route("widgets.index"),
                            "appearance"
                        );
                    }
                });
            }
        });
    }
}
