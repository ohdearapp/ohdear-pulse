<?php

namespace OhDear\OhDearPulse\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \OhDear\OhDearPulse\OhDearPulse
 */
class OhDearPulse extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \OhDear\OhDearPulse\OhDearPulse::class;
    }
}
