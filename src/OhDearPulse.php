<?php

namespace OhDear\OhDearPulse;

class OhDearPulse
{
    public static function isConfigured(): bool
    {
        return filled(config('services.oh_dear.pulse.api_key'));
    }
}
