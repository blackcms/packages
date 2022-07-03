<?php

namespace BlackCMS\Seo\Listeners;

use BlackCMS\Base\Events\UpdatedContentEvent;
use Exception;
use Seo;

class UpdatedContentListener
{
    /**
     * Handle the event.
     *
     * @param UpdatedContentEvent $event
     * @return void
     */
    public function handle(UpdatedContentEvent $event)
    {
        try {
            Seo::saveMetaData($event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
