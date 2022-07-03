<?php

namespace BlackCMS\Addon\Events;

use BlackCMS\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class ActivatedAddonEvent extends Event
{
    use SerializesModels;

    /**
     * @var string
     */
    public $addon;

    /**
     * ActivatedAddonEvent constructor.
     * @param string $addon
     */
    public function __construct(string $addon)
    {
        $this->addon = $addon;
    }
}
