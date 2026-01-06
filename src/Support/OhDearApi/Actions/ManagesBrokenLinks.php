<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Actions;

use OhDear\OhDearPulse\Support\OhDearApi\Resources\BrokenLink;

trait ManagesBrokenLinks
{
    public function brokenLinks(int $monitorId): array
    {
        return $this->transformCollection(
            $this->get("broken-links/$monitorId")['data'],
            BrokenLink::class
        );
    }
}
