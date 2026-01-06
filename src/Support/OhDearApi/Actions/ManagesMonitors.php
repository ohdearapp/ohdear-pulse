<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Actions;

use OhDear\OhDearPulse\Support\OhDearApi\Resources\Monitor;

trait ManagesMonitors
{
    public function site(int $monitorId): Monitor
    {
        $siteAttributes = $this->get("monitors/{$monitorId}");

        return new Monitor($siteAttributes, $this);
    }
}
