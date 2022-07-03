<?php

namespace BlackCMS\Menu\Providers;

use BlackCMS\Menu\Commands\ClearMenuCacheCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ClearMenuCacheCommand::class]);
        }
    }
}
