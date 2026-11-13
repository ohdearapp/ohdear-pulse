<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\PhpSdk\Dto\BrokenLink;

#[Lazy]
class OhDearBrokenLinksCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public function render(): Renderable
    {
        $this->apiError = null;
        $brokenLinks = $this->getBrokenLinks();

        return view('ohdear-pulse::brokenLinks', [
            'isConfigured' => $this->isConfigured(),
            'brokenLinks' => $brokenLinks,
            'apiError' => $this->apiError,
        ]);
    }

    /** @return list<BrokenLink>|null */
    protected function getBrokenLinks(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return $this->rememberApiCall(
            fn () => collect($this->ohDear()->brokenLinks($this->monitorId))->all(),
            'broken-links:'.$this->monitorId,
            CarbonInterval::minutes(15)
        );
    }
}
