<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use Livewire\Attributes\Locked;
use OhDear\OhDearPulse\OhDearPulse;
use OhDear\PhpSdk\OhDear;

trait UsesOhDearApi
{
    #[Locked]
    public ?int $monitorId = null;

    public function mount(?int $monitorId = null): void
    {
        $this->monitorId = $monitorId ?? config('services.oh_dear.pulse.monitor_id');
    }

    protected function css(): string
    {
        return __DIR__.'/../../../dist/output.css';
    }

    protected function ohDear(): OhDear
    {
        return app('ohdear-pulse');
    }

    public function isConfigured(): bool
    {
        if (! $this->monitorId || $this->monitorId < 1) {
            return false;
        }

        return OhDearPulse::isConfigured();
    }
}
