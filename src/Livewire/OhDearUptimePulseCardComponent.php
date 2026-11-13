<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\PhpSdk\Dto\Check;
use OhDear\PhpSdk\Dto\Monitor;
use OhDear\PhpSdk\Dto\UptimeMetric\HttpUptimeMetric;

#[Lazy]
class OhDearUptimePulseCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    public function render(): Renderable
    {
        $this->apiError = null;
        $monitor = $this->getMonitor();
        $check = collect($monitor->checks ?? [])->firstWhere('type', 'uptime');
        $check = $check ? Check::fromResponse($check) : null;

        $status = match (true) {
            $check === null => 'Not available',
            ! $check->enabled => 'Disabled',
            $check->activeSnooze !== null => 'Snoozed',
            default => $check->summary ?? 'Pending',
        };

        $color = match (true) {
            $check === null, ! $check->enabled, $check->activeSnooze !== null => 'gray',
            $check->checkResult()?->isWarning() === true => 'amber',
            $check->checkResult()?->isUp() === true => 'green',
            $check->checkResult()?->isDown() === true => 'red',
            default => 'gray',
        };

        return view('ohdear-pulse::uptime', [
            'monitor' => $monitor,
            'status' => $status,
            'color' => $color,
            'metrics' => $monitor?->type === 'http' ? $this->getMetrics() : [],
            'isConfigured' => $this->isConfigured(),
            'apiError' => $this->apiError,
        ]);
    }

    protected function getMonitor(): ?Monitor
    {
        if (! $this->isConfigured()) {
            return null;
        }

        return $this->rememberApiCall(
            fn () => $this->ohDear()->monitor($this->monitorId),
            'monitor:'.$this->monitorId,
            CarbonInterval::seconds(30),
        );
    }

    /** @return list<array{timestamp: int, milliseconds: float}> */
    protected function getMetrics(): array
    {
        $now = CarbonImmutable::now('UTC');

        $metrics = $this->rememberApiCall(
            fn () => $this->ohDear()->httpUptimeMetrics(
                $this->monitorId,
                $now->subMinutes(20)->toDateTimeString(),
                $now->toDateTimeString(),
            ),
            'http-uptime-metrics:'.$this->monitorId,
            CarbonInterval::seconds(30),
        );

        return collect($metrics ?? [])
            ->filter(fn (HttpUptimeMetric $metric) => $metric->totalTimeInSeconds > 0)
            ->map(fn (HttpUptimeMetric $metric) => [
                'timestamp' => CarbonImmutable::parse($metric->date, 'UTC')->getTimestamp(),
                'milliseconds' => round($metric->totalTimeInSeconds * 1000, 2),
            ])
            ->sortBy('timestamp')
            ->values()
            ->all();
    }
}
