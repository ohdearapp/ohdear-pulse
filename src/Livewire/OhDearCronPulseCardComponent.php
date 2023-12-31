<?php

namespace OhDear\OhDearPulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;

#[Lazy]
class OhDearCronPulseCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public ?int $siteId;

    protected function css()
    {
        return __DIR__.'/../../dist/output.css';
    }

    public function mount(?int $siteId = null)
    {
        $this->siteId = $siteId ?? config('services.oh_dear.pulse.site_id');
    }

    public function render(): Renderable
    {
        $cronChecks = $this->getCronChecks();

        return view('ohdear-pulse::cron', [
            'isConfigured' => $this->isConfigured(),
            'cronChecks' => $cronChecks,
        ]);
    }

    /**
     * @return array<\OhDear\PhpSdk\Resources\CronCheck>|null
     */
    public function getCronChecks(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return $this->rememberApiCall(
            fn () => $this->ohDear()?->cronChecks($this->siteId),
            'site:'.$this->siteId,
        );
    }
}
