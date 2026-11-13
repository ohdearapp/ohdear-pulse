<?php

namespace Workbench\App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::prependNamespace('pulse', __DIR__.'/../../resources/views/vendor/pulse');
    }
}
