<?php

namespace BlackCMS\Theme\Events;

use BlackCMS\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RenderingHomePageEvent extends Event
{
    use SerializesModels;
}
