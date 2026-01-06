<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Resources;

class CronCheck extends ApiResource
{
    public int $id;

    public string $name;

    public string $uuid;

    public string $type;

    public int $checkId;

    public ?int $frequencyInMinutes;

    public string $pingUrl;

    public int $graceTimeInMinutes = 0;

    public ?string $cronExpression = '';

    public ?string $humanReadableCronExpression = '';

    public ?string $description = '';

    public ?string $serverTimezone = '';

    public ?string $latestResult = '';

    public ?string $latestResultLabel = '';

    public ?string $latestResultLabelColor = '';

    public ?string $latestPingAt = '';

    public ?string $humanReadableLatestPingAt = '';

    public string $createdAt;

    public function __construct(array $attributes, $ohDear = null)
    {
        parent::__construct($attributes, $ohDear);
    }
}
