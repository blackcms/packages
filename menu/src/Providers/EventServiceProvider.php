<?php

namespace BlackCMS\Menu\Providers;

use BlackCMS\Base\Events\DeletedContentEvent;
use BlackCMS\Menu\Listeners\DeleteMenuNodeListener;
use BlackCMS\Menu\Listeners\UpdateMenuNodeUrlListener;
use BlackCMS\Slug\Events\UpdatedSlugEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdatedSlugEvent::class => [UpdateMenuNodeUrlListener::class],
        DeletedContentEvent::class => [DeleteMenuNodeListener::class],
    ];
}
