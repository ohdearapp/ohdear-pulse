<?php

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    MockClient::destroyGlobal();
    MockClient::global();
});

afterEach(fn () => MockClient::destroyGlobal());

it('verifies the configured monitor using the current SDK', function () {
    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(['id' => 123, 'team_id' => 1, 'url' => 'https://example.com']),
    ]);

    $this->artisan('ohdear-pulse:verify')
        ->expectsOutput('Connected to Oh Dear. Monitor 123: https://example.com')
        ->assertSuccessful();
});

it('fails helpfully for missing configuration', function (string $key, mixed $value) {
    config()->set("services.oh_dear.pulse.{$key}", $value);

    $this->artisan('ohdear-pulse:verify')
        ->expectsOutputToContain("services.oh_dear.pulse.{$key}")
        ->assertFailed();
})->with([
    ['api_key', null],
    ['api_key', ''],
    ['monitor_id', null],
    ['monitor_id', -1],
    ['monitor_id', 'not-an-id'],
]);

it('returns a failing exit code without exposing the API response', function () {
    MockClient::global()->addResponses([MockResponse::make(['message' => 'private response'], 404)]);

    $this->artisan('ohdear-pulse:verify')
        ->expectsOutput('Oh Dear returned HTTP 404. Check your token and monitor access.')
        ->assertFailed();
});
