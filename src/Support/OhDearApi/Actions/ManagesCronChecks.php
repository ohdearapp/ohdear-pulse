<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Actions;

use OhDear\OhDearPulse\Support\OhDearApi\Resources\CronCheck;

trait ManagesCronChecks
{
    public function cronChecks(int $monitorId)
    {
        return $this->transformCollection(
            $this->get("monitors/{$monitorId}/cron-checks")['data'],
            CronCheck::class
        );
    }
}
