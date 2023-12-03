<?php

namespace OhDear\OhDearPulse\Commands;

use Illuminate\Console\Command;

class OhDearPulseCommand extends Command
{
    public $signature = 'ohdear-pulse';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
