<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use OhDear\PhpSdk\OhDear;

trait UsesOhDearApi
{
    public function ohDear(): ?OhDear
    {
        return app('ohdear-pulse');
    }

}
