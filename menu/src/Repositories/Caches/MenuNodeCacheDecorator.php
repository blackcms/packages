<?php

namespace BlackCMS\Menu\Repositories\Caches;

use BlackCMS\Menu\Repositories\Interfaces\MenuNodeInterface;
use BlackCMS\Support\Repositories\Caches\CacheAbstractDecorator;

class MenuNodeCacheDecorator extends CacheAbstractDecorator implements
    MenuNodeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getByMenuId(
        $menuId,
        $parentId,
        $select = ["*"],
        array $with = ["child"]
    ) {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
