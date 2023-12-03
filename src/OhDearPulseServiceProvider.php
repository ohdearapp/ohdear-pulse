<?php

namespace OhDear\OhDearPulse;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use OhDear\OhDearPulse\Commands\OhDearPulseCommand;

class OhDearPulseServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('ohdear-pulse')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_ohdear-pulse_table')
            ->hasCommand(OhDearPulseCommand::class);
    }
}
