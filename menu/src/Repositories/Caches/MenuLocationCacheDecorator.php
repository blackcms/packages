<?php

namespace BlackCMS\Menu\Repositories\Caches;

use BlackCMS\Menu\Repositories\Interfaces\MenuLocationInterface;
use BlackCMS\Support\Repositories\Caches\CacheAbstractDecorator;

class MenuLocationCacheDecorator extends CacheAbstractDecorator implements
    MenuLocationInterface
{
}
