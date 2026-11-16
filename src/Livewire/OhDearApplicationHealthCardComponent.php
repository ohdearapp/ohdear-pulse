<?php

namespace OhDear\OhDearPulse\Livewire;

use Livewire\Attributes\Lazy;
use OhDear\PhpSdk\Dto\ApplicationHealthCheck;

#[Lazy]
class OhDearApplicationHealthCardComponent extends OhDearCheckCardComponent
{
    protected string $checkType = 'application_health';

    protected string $title = 'Application health';

    protected string $detailsView = 'applicationHealth';

    /** @return array{details: list<ApplicationHealthCheck>|null, hasResults: bool, color: string|null} */
    protected function getDetails(): array
    {
        $details = $this->rememberApiCall(
            fn () => $this->ohDear()->applicationHealthChecks($this->monitorId),
            'application-health:'.$this->monitorId,
        );

        $statuses = collect($details ?? [])
            ->reject(fn (ApplicationHealthCheck $check) => $check->activeSnooze !== null)
            ->pluck('status');

        return [
            'details' => $details,
            'hasResults' => count($details ?? []) > 0,
            'color' => match (true) {
                $statuses->intersect(['failed', 'crashed'])->isNotEmpty() => 'red',
                $statuses->contains('warning') => 'amber',
                default => null,
            },
        ];
    }
}
