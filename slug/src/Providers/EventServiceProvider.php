<?php

namespace BlackCMS\Slug\Providers;

use BlackCMS\Base\Events\CreatedContentEvent;
use BlackCMS\Base\Events\DeletedContentEvent;
use BlackCMS\Base\Events\UpdatedContentEvent;
use BlackCMS\Slug\Listeners\CreatedContentListener;
use BlackCMS\Slug\Listeners\DeletedContentListener;
use BlackCMS\Slug\Listeners\UpdatedContentListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        UpdatedContentEvent::class => [UpdatedContentListener::class],
        CreatedContentEvent::class => [CreatedContentListener::class],
        DeletedContentEvent::class => [DeletedContentListener::class],
    ];
}
