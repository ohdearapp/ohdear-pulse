<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use Carbon\CarbonInterval;
use Illuminate\Support\Facades\App;
use Laravel\Pulse\Support\CacheStoreResolver;
use OhDear\OhDearPulse\Exceptions\SiteIdNotCorrect;
use OhDear\PhpSdk\Exceptions\NotFoundException;

trait RemembersApiCalls
{
    public function rememberApiCall(callable $apiCall, string $key, ?CarbonInterval $interval = null): mixed
    {
        $interval ??= CarbonInterval::minute();

        return App::make(CacheStoreResolver::class)->store()->remember(
            'laravel:pulse:'.static::class.':'.$key,
            $interval,
            function () use ($apiCall) {
                try {
                    return $apiCall();
                } catch (NotFoundException $exception) {
                    throw SiteIdNotCorrect::make($exception);
                }

            },
        );
    }
}
