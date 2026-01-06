<?php

namespace OhDear\OhDearPulse\Commands;

use Exception;
use Illuminate\Console\Command;
use OhDear\PhpSdk\OhDear;

class VerifyCommand extends Command
{
    public $signature = 'ohdear-pulse:verify';

    public $description = 'Verify that the Oh Dear connection is configured correctly';

    public function handle()
    {
        $ohDearConfig = config('services.oh_dear.pulse');

        $this->info('Verifying if Oh Dear is configured correctly...');

        $this
            ->verifySdkInstalled()
            ->verifyApiToken($ohDearConfig)
            ->verifySiteId($ohDearConfig)
            ->verifyConnection($ohDearConfig);

        $this->info('✅ Oh Dear is configured correctly!');
    }

    public function verifySdkInstalled(): self
    {
        if (! class_exists(OhDear::class)) {
            throw new Exception('You must install the Oh Dear SDK in order to sync your schedule with Oh Dear. Run `composer require ohdearapp/ohdear-php-sdk`.');
        }

        $this->comment('The Oh Dear SDK is installed.');

        return $this;
    }

    protected function verifyApiToken(array $ohDearConfig): self
    {
        if (empty($ohDearConfig['api_key'])) {
            throw new Exception('No API key found. Make sure you added an API token to the `services.oh_dear.pulse.api_key` key of the `services` config file. You can generate a new token here: https://ohdear.app/user/api-tokens');
        }

        $this->comment('Oh Dear API token found.');

        return $this;
    }

    protected function verifySiteId(array $ohDearConfig): self
    {
        if (empty($ohDearConfig['monitor_id'])) {
            throw new Exception('No site id found. Make sure you added an site id to the `oh_dear.pulse.monitor_id` key of the `services` config file. You can found your site id on the settings page of a site on Oh Dear.');
        }

        $this->comment('Oh Dear site id found.');

        return $this;
    }

    protected function verifyConnection(array $ohDearConfig): self
    {
        $this->comment('Trying to reach Oh Dear...');

        $site = app('ohdear-pulse')->site($ohDearConfig['monitor_id']);

        $this->comment("Successfully connected to Oh Dear. The configured site URL is: {$site->sortUrl}");

        return $this;
    }
}
