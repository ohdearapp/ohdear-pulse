<?php

namespace OhDear\OhDearPulse\Livewire;

use Carbon\CarbonInterval;
use Illuminate\Contracts\Support\Renderable;
use Laravel\Pulse\Livewire\Card;
use OhDear\OhDearPulse\Livewire\Concerns\RemembersApiCalls;
use OhDear\OhDearPulse\Livewire\Concerns\UsesOhDearApi;
use OhDear\PhpSdk\Dto\Check;

abstract class OhDearCheckCardComponent extends Card
{
    use RemembersApiCalls;
    use UsesOhDearApi;

    protected string $checkType;

    protected string $title;

    protected string $detailsView;

    public function render(): Renderable
    {
        $this->apiError = null;
        $monitor = $this->isConfigured()
            ? $this->rememberApiCall(fn () => $this->ohDear()->monitor($this->monitorId), 'monitor:'.$this->monitorId, CarbonInterval::seconds(30))
            : null;
        $checkData = collect($monitor->checks ?? [])->firstWhere('type', $this->checkType);
        $check = $checkData ? Check::fromResponse($checkData) : null;
        $result = $check?->checkResult();
        $hasResult = $result !== null && ! $result->isPending();
        $canLoadDetails = $check?->enabled && $check->activeSnooze === null && $hasResult;
        $data = $canLoadDetails ? $this->getDetails() : ['details' => null, 'hasResults' => false];

        $status = match (true) {
            $check === null => 'Not available',
            ! $check->enabled => 'Disabled',
            $check->activeSnooze !== null => 'Snoozed',
            ! $hasResult => $check->summary ?: 'Pending',
            $result->isWarning() => 'Warning',
            $result->isDown() => 'Failing',
            default => 'Healthy',
        };

        $color = match (true) {
            ! $canLoadDetails => 'gray',
            $result->isWarning() => 'amber',
            $result->isDown() => 'red',
            default => 'green',
        };

        if ($color === 'green' && ! $data['hasResults']) {
            $status = 'No results';
            $color = 'gray';
        }

        if (($data['color'] ?? null) === 'red') {
            $status = 'Failing';
            $color = 'red';
        }

        if ($color === 'green' && ($data['color'] ?? null) === 'amber') {
            $status = 'Warning';
            $color = 'amber';
        }

        return view('ohdear-pulse::check', [
            'title' => $this->title,
            'report' => $this->checkType === 'application_health'
                ? 'application-health/run'
                : str_replace('_', '-', $this->checkType).'/report',
            'detailsView' => $this->detailsView,
            'isConfigured' => $this->isConfigured(),
            'apiError' => $this->apiError,
            'monitor' => $monitor,
            'check' => $check,
            'canLoadDetails' => $canLoadDetails,
            'details' => $data['details'],
            'status' => $status,
            'color' => $color,
        ]);
    }

    public function placeholder(): Renderable
    {
        return view('ohdear-pulse::checkLoading', ['title' => $this->title]);
    }

    /** @return array{details: mixed, hasResults: bool, color?: string|null} */
    abstract protected function getDetails(): array;
}
