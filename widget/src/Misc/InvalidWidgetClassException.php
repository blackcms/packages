<?php

namespace BlackCMS\Widget\Misc;

use BlackCMS\Widget\AbstractWidget;
use Exception;

class InvalidWidgetClassException extends Exception
{
    /**
     * Exception message.
     *
     * @var string
     */
    protected $message =
        "Widget class must extend class " . AbstractWidget::class;
}
