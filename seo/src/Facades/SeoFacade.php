<?php

namespace BlackCMS\Seo\Facades;

use BlackCMS\Seo\Seo;
use Illuminate\Support\Facades\Facade;

/**
 * @since 02/12/2015 14:08 PM
 */
class SeoFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return Seo::class;
    }
}
