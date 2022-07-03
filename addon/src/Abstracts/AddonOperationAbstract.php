<?php

namespace BlackCMS\Addon\Abstracts;

abstract class AddonOperationAbstract
{
    public static function activate()
    {
        // Run when activating a addon
    }

    public static function activated()
    {
        // Run when a addon is activated
    }

    public static function deactivate()
    {
        // Run when deactivating a addon
    }

    public static function deactivated()
    {
        // Run when a addon is deactivated
    }

    public static function remove()
    {
        // Run when remove a addon
    }

    public static function removed()
    {
        // Run when removed a addon
    }
}
