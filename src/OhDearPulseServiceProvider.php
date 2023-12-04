<?php

namespace OhDear\OhDearPulse;

use Livewire\Livewire;
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

        $this->app->bind('ohdear-pulse', function () {
            if(! config('services.ohdear-pulse.api_key')) {
                return null;
            }

            return new OhDear(config('services.ohdear-pulse.api_key'));
        });
    }
}
