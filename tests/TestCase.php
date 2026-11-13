<?php

namespace OhDear\OhDearPulse\Tests;

use Laravel\Pulse\PulseServiceProvider;
use Livewire\LivewireServiceProvider;
use OhDear\OhDearPulse\OhDearPulseServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            PulseServiceProvider::class,
            OhDearPulseServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', str_repeat('a', 32));
        config()->set('cache.default', 'array');
        config()->set('pulse.enabled', false);
        config()->set('services.oh_dear.pulse', [
            'api_key' => 'test-token',
            'monitor_id' => 123,
        ]);
    }
}
