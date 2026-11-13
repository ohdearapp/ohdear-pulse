<?php

namespace OhDear\OhDearPulse\Livewire\Concerns;

use Carbon\CarbonInterval;
use Closure;
use Illuminate\Support\Facades\App;
use Laravel\Pulse\Support\CacheStoreResolver;
use OhDear\PhpSdk\Exceptions\OhDearException;
use Saloon\Exceptions\Request\FatalRequestException;

trait RemembersApiCalls
{
    protected ?string $apiError = null;

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $apiCall
     * @return TValue|null
     */
    protected function rememberApiCall(Closure $apiCall, string $key, ?CarbonInterval $interval = null): mixed
    {
        $interval ??= CarbonInterval::minute();

        $tokenHash = hash('sha256', config('services.oh_dear.pulse.api_key'));

        try {
            return App::make(CacheStoreResolver::class)->store()->remember(
                "laravel:pulse:ohdear:v2:{$tokenHash}:{$key}",
                $interval,
                $apiCall,
            );
        } catch (OhDearException $exception) {
            $this->apiError = match ($exception->response->status()) {
                401, 403 => 'Oh Dear could not authorize this request. Check your API token and monitor access.',
                404 => 'Monitor not found. Check the monitor ID and your API token’s access.',
                429 => 'Oh Dear’s API rate limit was reached. This card will retry automatically.',
                default => 'Oh Dear is unavailable. This card will retry automatically.',
            };
        } catch (FatalRequestException $exception) {
            $this->apiError = 'Could not connect to Oh Dear. This card will retry automatically.';
        }

        return null;
    }
}
