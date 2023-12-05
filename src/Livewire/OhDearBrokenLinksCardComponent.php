<?php

namespace OhDear\OhDearPulse\Livewire;

use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\OhDearPulse\OhDearPulse;
use OhDear\PhpSdk\Resources\Check;
use OhDear\PhpSdk\Resources\Site;

#[Lazy]
class OhDearBrokenLinksCardComponent extends Card
{
    use UsesOhDearApi;
    use RemembersApiCalls;

    public int $siteId;

    public function mount(int $siteId = null)
    {
        $this->siteId = $siteId ?? config('services.oh_dear.pulse.site_id');
    }

    public function render(): Renderable
    {
        $brokenLinks = $this->getBrokenLinks();

        return view('ohdear-pulse::brokenLinks', [
            'brokenLinks' => $brokenLinks,
        ]);
    }

    /**
     * @return array<\OhDear\PhpSdk\Resources\BrokenLink>|null
     */
    public function getBrokenLinks(): ?array
    {
        if (! OhDearPulse::isConfigured()) {
            return null;
        }

        return $this->remember(
            fn() => $this->ohDear()?->brokenLinks($this->siteId),
            'site:' . $this->siteId,
        );
    }
}
