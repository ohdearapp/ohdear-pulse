<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use OhDear\OhDearPulse\OhDearPulse;
use OhDear\OhDearPulse\Support\OhDearApi\OhDear;

trait UsesOhDearApi
{
    public function ohDear(): ?OhDear
    {
        return app('ohdear-pulse');
    }

    public function isConfigured(): bool
    {
        if (! $this->monitorId) {
            return false;
        }

        if (! OhDearPulse::isConfigured()) {
            return false;
        }

        return true;
    }
}
