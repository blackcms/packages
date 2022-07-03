<?php

namespace BlackCMS\Addon\Providers;

use BlackCMS\Addon\Commands\AddonActivateAllCommand;
use BlackCMS\Addon\Commands\AddonActivateCommand;
use BlackCMS\Addon\Commands\AddonAssetsPublishCommand;
use BlackCMS\Addon\Commands\AddonDeactivateAllCommand;
use BlackCMS\Addon\Commands\AddonDeactivateCommand;
use BlackCMS\Addon\Commands\AddonRemoveCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([AddonAssetsPublishCommand::class]);
        }

        $this->commands([
            AddonActivateCommand::class,
            AddonDeactivateCommand::class,
            AddonRemoveCommand::class,
            AddonActivateAllCommand::class,
            AddonDeactivateAllCommand::class,
        ]);
    }
}
