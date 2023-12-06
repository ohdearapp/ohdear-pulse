<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\OhDearPulse\OhDearPulse;
use OhDear\PhpSdk\Resources\Check;
use OhDear\PhpSdk\Resources\Site;

#[Lazy]
class OhDearUptimePulseCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public int $siteId;

    protected function css()
    {
        return __DIR__.'/../../dist/output.css';
    }

    public function mount(?int $siteId = null)
    {
        $this->sites = collect();

        $this->siteId = $siteId ?? config('services.oh_dear.pulse.site_id');
    }

    public function render(): Renderable
    {
        $site = $this->getSite();

        return view('ohdear-pulse::uptime', [
            'site' => $site,
            'status' => $this->getStatus($site),
            'performance' => $this->getPerformance($site),
        ]);
    }

    public function getSite(): ?Site
    {
        if (! OhDearPulse::isConfigured()) {
            return null;
        }

        $siteAttributes = $this->remember(
            fn () => $this->ohDear()?->site($this->siteId)?->attributes,
            'site:'.$this->siteId,
            CarbonInterval::seconds(10),
        );

        return new Site($siteAttributes);
    }

    protected function getStatus(?Site $site): ?string
    {
        if (! $site) {
            return null;
        }

        if (! $check = $this->getCheck($site, 'uptime')) {
            return null;
        }

        return $check->summary;
    }

    protected function getPerformance(?Site $site): ?string
    {
        if (! $site) {
            return null;
        }

        if (! $check = $this->getCheck($site, 'performance')) {
            return null;
        }

        return $check->summary;
    }

    protected function getCheck(Site $site, string $type): ?Check
    {
        return collect($site->checks)
            ->first(fn (Check $check) => $check->type === $type);
    }
}
