<?php

namespace OhDear\OhDearPulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\PhpSdk\Dto\CronCheckDefinition;

#[Lazy]
class OhDearCronPulseCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public function render(): Renderable
    {
        $this->apiError = null;
        $cronChecks = $this->getCronChecks();

        return view('ohdear-pulse::cron', [
            'isConfigured' => $this->isConfigured(),
            'cronChecks' => $cronChecks,
            'apiError' => $this->apiError,
        ]);
    }

    /** @return list<CronCheckDefinition>|null */
    protected function getCronChecks(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return $this->rememberApiCall(
            fn () => collect($this->ohDear()->cronCheckDefinitions($this->monitorId))->all(),
            'cron-checks:'.$this->monitorId,
        );
    }
}
