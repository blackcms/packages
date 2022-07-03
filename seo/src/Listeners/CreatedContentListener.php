<?php

namespace BlackCMS\Seo\Listeners;

use BlackCMS\Base\Events\CreatedContentEvent;
use Exception;
use Seo;

class CreatedContentListener
{
    /**
     * Handle the event.
     *
     * @param CreatedContentEvent $event
     * @return void
     */
    public function handle(CreatedContentEvent $event)
    {
        try {
            Seo::saveMetaData($event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
