<?php

namespace BlackCMS\Slug\Facades;

use BlackCMS\Slug\SlugHelper;
use Illuminate\Support\Facades\Facade;

class SlugHelperFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return SlugHelper::class;
    }
}
