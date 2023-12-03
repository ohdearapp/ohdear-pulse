<?php

namespace OhDear\OhDearPulse;

use Livewire\Livewire;
use OhDear\OhDearPulse\Livewire\OhDearUptimePulseCardComponent;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use OhDear\OhDearPulse\Commands\OhDearPulseCommand;

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
    }
}
