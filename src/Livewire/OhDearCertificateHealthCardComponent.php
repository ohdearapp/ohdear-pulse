<?php

namespace OhDear\OhDearPulse\Livewire;

use Livewire\Attributes\Lazy;
use OhDear\PhpSdk\Dto\CertificateHealth;

#[Lazy]
class OhDearCertificateHealthCardComponent extends OhDearCheckCardComponent
{
    protected string $checkType = 'certificate_health';

    protected string $title = 'Certificate health';

    protected string $detailsView = 'certificateHealth';

    /** @return array{details: CertificateHealth|null, hasResults: bool} */
    protected function getDetails(): array
    {
        $details = $this->rememberApiCall(
            fn () => $this->ohDear()->certificateHealth($this->monitorId),
            'certificate-health:'.$this->monitorId,
        );

        return [
            'details' => $details,
            'hasResults' => $details !== null && ($details->certificateDetails || $details->certificateChecks),
        ];
    }
}
