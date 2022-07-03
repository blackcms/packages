<?php

namespace BlackCMS\Widget\Repositories\Caches;

use BlackCMS\Support\Repositories\Caches\CacheAbstractDecorator;
use BlackCMS\Widget\Repositories\Interfaces\WidgetInterface;

class WidgetCacheDecorator extends CacheAbstractDecorator implements
    WidgetInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByTheme($theme)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
