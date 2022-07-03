<?php

namespace BlackCMS\Seo\Contracts\Entities;

use BlackCMS\Seo\Contracts\RenderableContract;

interface AnalyticsContract extends RenderableContract
{
    /**
     * Set Google Analytics code.
     *
     * @param string $code
     * @return $this
     */
    public function setGoogle($code);
}
