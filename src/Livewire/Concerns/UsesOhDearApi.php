<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use OhDear\OhDearPulse\OhDearPulse;
use OhDear\PhpSdk\OhDear;

trait UsesOhDearApi
{
    public function ohDear(): ?OhDear
    {
        return app('ohdear-pulse');
    }

    public function isConfigured(): bool
    {
        if (! $this->siteId) {
            return false;
        }

        if (! OhDearPulse::isConfigured()) {
            return false;
        }

        return true;
    }
}
