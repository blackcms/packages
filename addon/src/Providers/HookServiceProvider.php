<?php

namespace BlackCMS\Addon\Providers;

use BlackCMS\Dashboard\Supports\WidgetInstance as WidgetInstance;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Throwable;

class HookServiceProvider extends ServiceProvider
{
    public function boot()
    {
        add_filter(
            DASHBOARD_FILTER_ADMIN_LIST,
            [$this, "addStatsWidgets"],
            15,
            2
        );
    }

    /**
     * @param array $widgets
     * @param Collection $widgetSettings
     * @return array
     * @throws Throwable
     */
    public function addStatsWidgets($widgets, $widgetSettings)
    {
        $addons = count(scan_folder(addon_path()));

        return (new WidgetInstance())
            ->setType("stats")
            ->setPermission("addons.index")
            ->setTitle(trans("packages/addon::addon.addons"))
            ->setKey("widget_total_addons")
            ->setIcon("las la-puzzle-piece")
            ->setColor("#8e44ad")
            ->setStatsTotal($addons)
            ->setRoute(route("addons.index"))
            ->init($widgets, $widgetSettings);
    }
}
