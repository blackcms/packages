<?php

namespace BlackCMS\Theme\Events;

use BlackCMS\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

class RenderingSiteMapEvent extends Event
{
    use SerializesModels;
}
