<?php

namespace OhDear\OhDearPulse;

use Livewire\LivewireManager;
use OhDear\OhDearPulse\Commands\VerifyCommand;
use OhDear\OhDearPulse\Livewire\OhDearApplicationHealthCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearBrokenLinksCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearCertificateHealthCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearCronPulseCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearDomainCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearUptimePulseCardComponent;
use OhDear\PhpSdk\OhDear;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class OhDearPulseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('ohdear-pulse')
            ->hasViews()
            ->hasCommand(VerifyCommand::class);
    }

    public function packageBooted(): void
    {
        $this->callAfterResolving('livewire', function (LivewireManager $livewire) {
            $livewire->component('ohdear.pulse.uptime', OhDearUptimePulseCardComponent::class);
            $livewire->component('ohdear.pulse.cron', OhDearCronPulseCardComponent::class);
            $livewire->component('ohdear.pulse.brokenLinks', OhDearBrokenLinksCardComponent::class);
            $livewire->component('ohdear.pulse.certificateHealth', OhDearCertificateHealthCardComponent::class);
            $livewire->component('ohdear.pulse.domain', OhDearDomainCardComponent::class);
            $livewire->component('ohdear.pulse.applicationHealth', OhDearApplicationHealthCardComponent::class);
        });

        $this->app->bind('ohdear-pulse', function () {
            return new OhDear(config('services.oh_dear.pulse.api_key', ''));
        });
    }
}
