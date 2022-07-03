<?php

namespace BlackCMS\Seo\Listeners;

use BlackCMS\Base\Events\DeletedContentEvent;
use Exception;
use Seo;

class DeletedContentListener
{
    /**
     * Handle the event.
     *
     * @param DeletedContentEvent $event
     * @return void
     */
    public function handle(DeletedContentEvent $event)
    {
        try {
            Seo::deleteMetaData($event->screen, $event->data);
        } catch (Exception $exception) {
            info($exception->getMessage());
        }
    }
}
