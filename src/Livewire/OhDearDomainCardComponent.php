<?php

namespace OhDear\OhDearPulse\Livewire;

use Livewire\Attributes\Lazy;
use OhDear\PhpSdk\Dto\DomainInfo;

#[Lazy]
class OhDearDomainCardComponent extends OhDearCheckCardComponent
{
    protected string $checkType = 'domain';

    protected string $title = 'Domain expiry';

    protected string $detailsView = 'domain';

    /** @return array{details: DomainInfo|null, hasResults: bool} */
    protected function getDetails(): array
    {
        $details = $this->rememberApiCall(
            fn () => $this->ohDear()->domain($this->monitorId),
            'domain:'.$this->monitorId,
        );

        return [
            'details' => $details,
            'hasResults' => $details !== null && ($details->expiresAt || $details->registeredAt || $details->domainStatuses),
        ];
    }
}
