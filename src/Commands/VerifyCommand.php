<?php

namespace OhDear\OhDearPulse\Commands;

use Illuminate\Console\Command;
use OhDear\OhDearPulse\OhDearPulse;
use OhDear\PhpSdk\Exceptions\OhDearException;
use Saloon\Exceptions\Request\FatalRequestException;

class VerifyCommand extends Command
{
    public $signature = 'ohdear-pulse:verify';

    public $description = 'Verify the Oh Dear API token and monitor ID';

    public function handle(): int
    {
        if (! OhDearPulse::isConfigured()) {
            $this->error('Set services.oh_dear.pulse.api_key to your Oh Dear API token.');

            return self::FAILURE;
        }

        $monitorId = config('services.oh_dear.pulse.monitor_id');

        if (! filter_var($monitorId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $this->error('Set services.oh_dear.pulse.monitor_id to a valid Oh Dear monitor ID.');

            return self::FAILURE;
        }

        $this->comment('Connecting to Oh Dear...');

        try {
            $monitor = app('ohdear-pulse')->monitor((int) $monitorId);
        } catch (OhDearException $exception) {
            $this->error("Oh Dear returned HTTP {$exception->response->status()}. Check your token and monitor access.");

            return self::FAILURE;
        } catch (FatalRequestException $exception) {
            $this->error('Could not connect to Oh Dear. Check your network connection and try again.');

            return self::FAILURE;
        }

        $this->info("Connected to Oh Dear. Monitor {$monitor->id}: {$monitor->sortUrl}");

        return self::SUCCESS;
    }
}
