<?php

namespace OhDear\OhDearPulse;

class OhDearPulse
{
    public static function isConfigured()
    {
        return config('services.oh_dear.pulse.api_key') !== null;
    }


}
