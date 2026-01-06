<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;

#[Lazy]
class OhDearBrokenLinksCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public ?int $monitorId = null;

    public function mount(?int $monitorId = null)
    {
        $this->monitorId = $monitorId ?? config('services.oh_dear.pulse.monitor_id');
    }

    public function render(): Renderable
    {
        $brokenLinks = $this->getBrokenLinks();

        return view('ohdear-pulse::brokenLinks', [
            'isConfigured' => $this->isConfigured(),
            'brokenLinks' => $brokenLinks,
        ]);
    }

    /**
     * @return array<\OhDear\OhDearPulse\Support\OhDearApi\Resources\BrokenLink>|null
     */
    public function getBrokenLinks(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return $this->rememberApiCall(
            fn () => $this->ohDear()?->brokenLinks($this->monitorId),
            'monitor:'.$this->monitorId,
            CarbonInterval::minutes(15)
        );
    }
}
