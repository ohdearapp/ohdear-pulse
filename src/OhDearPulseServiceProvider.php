<?php

namespace OhDear\OhDearPulse;

use Livewire\Livewire;
use Livewire\LivewireManager;
use OhDear\OhDearPulse\Livewire\OhDearBrokenLinksCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearCronPulseCardComponent;
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
            ->hasConfigFile()
            ->hasViews();
    }

    public function packageBooted()
    {
        $this->callAfterResolving('livewire', function (LivewireManager $livewire) {
            $livewire->component('ohdear.pulse.uptime', OhDearUptimePulseCardComponent::class);
            $livewire->component('ohdear.pulse.cron', OhDearCronPulseCardComponent::class);
            $livewire->component('ohdear.pulse.brokenLinks', OhDearBrokenLinksCardComponent::class);
        });

        $this->app->bind('ohdear-pulse', function () {
            if (! OhDearPulse::isConfigured()) {
                return null;
            }

            return new OhDear(config('services.oh_dear.pulse.api_key'));
        });
    }
}
