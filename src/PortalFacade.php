<?php

namespace RobertBoes\LaravelPortal;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Robertboes\LaravelPortal\Portal
 */
class PortalFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-portal';
    }
}
