<?php

namespace BlackCMS\Theme\Events;

use BlackCMS\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class ThemeRemoveEvent extends Event
{
    use SerializesModels;

    /**
     * @var string
     */
    public $theme;

    /**
     * ThemeRemoveEvent constructor.
     * @param string
     */
    public function __construct($theme)
    {
        $this->theme = $theme;
    }
}
