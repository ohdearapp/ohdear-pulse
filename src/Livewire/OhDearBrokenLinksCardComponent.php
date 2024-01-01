<?php

namespace OhDear\OhDearPulse\Livewire;

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

    public ?int $siteId = null;

    public function mount(?int $siteId = null)
    {
        $this->siteId = $siteId ?? config('services.oh_dear.pulse.site_id');
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
     * @return array<\OhDear\PhpSdk\Resources\BrokenLink>|null
     */
    public function getBrokenLinks(): ?array
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return ray()->pass($this->remember(
            fn () => $this->ohDear()?->brokenLinks($this->siteId),
            'site:'.$this->siteId,
        ));
    }
}
