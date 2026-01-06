<?php

namespace OhDear\OhDearPulse\Support\OhDearApi;

use DateTimeImmutable;
use GuzzleHttp\Client;
use OhDear\OhDearPulse\Support\OhDearApi\Actions\ManagesBrokenLinks;
use OhDear\OhDearPulse\Support\OhDearApi\Actions\ManagesCronChecks;
use OhDear\OhDearPulse\Support\OhDearApi\Actions\ManagesMonitors;
use OhDear\OhDearPulse\Support\OhDearApi\Actions\ManagesPerformance;

class OhDear
{
    use MakesHttpRequests;
    use ManagesBrokenLinks;
    use ManagesCronChecks;
    use ManagesMonitors;
    use ManagesPerformance;

    public string $apiToken;

    public Client $client;

    public function __construct(string $apiToken, string $baseUri = 'https://ohdear.app/api/')
    {
        $this->apiToken = $apiToken;

        $this->client = new Client([
            'base_uri' => $baseUri,
            'http_errors' => false,
            'headers' => [
                'Authorization' => "Bearer {$this->apiToken}",
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function setClient(Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    protected function transformCollection(array $collection, string $class): array
    {
        return array_map(function ($attributes) use ($class) {
            return new $class($attributes, $this);
        }, $collection);
    }

    public function convertDateFormat(string $date, $format = 'YmdHis'): string
    {
        return (new DateTimeImmutable($date))->format($format);
    }
}
