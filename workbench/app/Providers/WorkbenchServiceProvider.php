<?php

namespace Workbench\App\Providers;

use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Pulse\Facades\Pulse;
use Workbench\App\Http\Middleware\PreviewCheckCards;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::prependNamespace('pulse', __DIR__.'/../../resources/views/vendor/pulse');

        if ($this->app->environment('local')) {
            TrustProxies::at(['127.0.0.1', '::1']);
            Pulse::css(__DIR__.'/../../../dist/output.css');
            $this->app['router']->pushMiddlewareToGroup('web', PreviewCheckCards::class);
        }
    }
}
