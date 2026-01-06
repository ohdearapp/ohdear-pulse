<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Resources;

class PerformanceRecord extends ApiResource
{
    public int $id;

    public int $monitorId;

    public string $createdAt;

    public float $dnsTimeInSeconds;

    public float $tcpTimeInSeconds;

    public float $sslHandshakeTimeInSeconds;

    public float $remoteServerProcessingTimeInSeconds;

    public float $downloadTimeInSeconds;

    public float $totalTimeInSeconds;

    public array $curl;
}
