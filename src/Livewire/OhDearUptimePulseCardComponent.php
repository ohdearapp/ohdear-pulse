<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\Carbon;
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

    public function getData()
    {
        return collect([
            [
                now()->timestamp * 1000,
                70,
            ],
            [
                now()->addMinutes(-1)->timestamp * 1000,
                80,
            ],
            [
                now()->addMinutes(-2)->timestamp * 1000,
                95,
            ],
            [
                now()->addMinutes(-3)->timestamp * 1000,
                75,
            ],
            [
                now()->addMinutes(-4)->timestamp * 1000,
                75,
            ],
            [
                now()->addMinutes(-5)->timestamp * 1000,
                80,
            ],
            [
                now()->addMinutes(-6)->timestamp * 1000,
                90,
            ],
            [
                now()->addMinutes(-7)->timestamp * 1000,
                85,
            ],
            [
                now()->addMinutes(-8)->timestamp * 1000,
                80,
            ],
            [
                now()->addMinutes(-9)->timestamp * 1000,
                60,
            ],
            [
                now()->addMinutes(-10)->timestamp * 1000,
                75,
            ],
            [
                now()->addMinutes(-11)->timestamp * 1000,
                80,
            ],
        ])->toArray();
    }

    protected function getLabels(): array
    {
        return collect($this->getData())
            ->map(function (array $dataPoint) {
                return Carbon::createFromTimestamp($dataPoint[0] / 1000)
                    ->format('Y-m-d H:i');
            })
            ->toArray();
    }

    public function render(): Renderable
    {
        $site = $this->getSite();

        return view('ohdear-pulse::uptime', [
            'site' => $site,
            'status' => $this->getStatus($site),
            'statusColor' => $this->getStatusColor(),
            'performance' => $this->getPerformance($site),
            'isConfigured' => $this->isConfigured(),
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

        return match ($check->summary) {
            'Up' => 'Online',
            default => $check->summary,
        };
    }

    protected function getStatusColor()
    {
        return match ($this->getStatus($this->getSite())) {
            'Online' => 'dark:bg-gradient-to-t dark:from-emerald-500 dark:to-emerald-400 bg-emerald-100 text-emerald-800 dark:border-emerald-300 dark:text-gray-900 dark:border-t',
            default => 'bg-gray-600',
        };
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
