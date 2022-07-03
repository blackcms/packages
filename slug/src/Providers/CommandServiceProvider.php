<?php

namespace BlackCMS\Slug\Providers;

use BlackCMS\Slug\Commands\ChangeSlugPrefixCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([ChangeSlugPrefixCommand::class]);
        }
    }
}
