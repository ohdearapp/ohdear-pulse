<?php

namespace OhDear\OhDearPulse;

use Livewire\Livewire;
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
        Livewire::component('ohdear.pulse.uptime', OhDearUptimePulseCardComponent::class);
        Livewire::component('ohdear.pulse.cron', OhDearCronPulseCardComponent::class);

        $this->app->bind('ohdear-pulse', function () {
            if(! OhDearPulse::isConfigured()) {
                return null;
            }

            return new OhDear(config('services.oh_dear.pulse.api_key'));
        });
    }
}
