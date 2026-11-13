<?php

use Carbon\CarbonImmutable;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use OhDear\OhDearPulse\Livewire\OhDearBrokenLinksCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearCronPulseCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearUptimePulseCardComponent;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;

beforeEach(function () {
    MockClient::destroyGlobal();
    MockClient::global();
    Livewire::withoutLazyLoading();
});

afterEach(function () {
    MockClient::destroyGlobal();
});

dataset('cards', [
    'uptime' => OhDearUptimePulseCardComponent::class,
    'scheduled tasks' => OhDearCronPulseCardComponent::class,
    'broken links' => OhDearBrokenLinksCardComponent::class,
]);

it('renders configuration instructions without contacting the API', function (string $component, string $key, mixed $value) {
    config()->set("services.oh_dear.pulse.{$key}", $value);

    Livewire::test($component)->assertSee('Connect your monitor');
})->with('cards')->with([
    ['api_key', null],
    ['api_key', ''],
    ['monitor_id', null],
    ['monitor_id', 0],
]);

it('uses current uptime metrics with the correct time window, numeric units and chronological order', function () {
    CarbonImmutable::setTestNow('2026-11-13 12:20:00 UTC');

    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData()),
        'https://ohdear.app/api/monitors/123/http-uptime-metrics*' => function (PendingRequest $request) {
            expect($request->query()->all())->toBe([
                'filter[start]' => '20261113120000',
                'filter[end]' => '20261113122000',
                'filter[group_by]' => 'minute',
            ]);

            return MockResponse::make(['data' => [
                metricData('2026-11-13 12:20:00', 0),
                metricData('2026-11-13 12:19:00', 2.5),
                metricData('2026-11-13 12:01:00', 12.5),
                metricData('2026-11-13 12:10:00', 0.123),
            ]]);
        },
    ]);

    Livewire::test(OhDearUptimePulseCardComponent::class)
        ->assertSee('example.com')
        ->assertSee('Up')
        ->assertSee('2,500')
        ->assertSee('Latest 2500 milliseconds.')
        ->assertSee('19 minutes ago')
        ->assertSee('1 minute ago')
        ->assertSeeHtml('<polyline');

    CarbonImmutable::setTestNow();
});

it('renders empty and single measurement charts', function (array $metrics, string $expected) {
    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData()),
        'https://ohdear.app/api/monitors/123/http-uptime-metrics*' => MockResponse::make(['data' => $metrics]),
    ]);

    Livewire::test(OhDearUptimePulseCardComponent::class)->assertSee($expected);
})->with([
    'empty' => [[], 'No response times yet'],
    'unmeasured buckets' => [[metricData('2026-11-13 12:00:00', 0)], 'No response times yet'],
    'one measurement' => [[metricData('2026-11-13 12:00:00', 0.234)], 'Latest 234 milliseconds.'],
]);

it('does not present disabled, snoozed, down or pending checks as healthy', function (array $check, string $status, string $color) {
    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData([
            'checks' => [array_replace(monitorData()['checks'][0], $check)],
        ])),
        'https://ohdear.app/api/monitors/123/http-uptime-metrics*' => MockResponse::make(['data' => []]),
    ]);

    Livewire::test(OhDearUptimePulseCardComponent::class)
        ->assertSee($status)
        ->assertViewHas('color', $color);
})->with([
    'disabled' => [['enabled' => false], 'Disabled', 'gray'],
    'snoozed' => [['active_snooze' => ['ends_at' => '2026-11-13 13:00:00']], 'Snoozed', 'gray'],
    'down' => [['latest_run_result' => 'failed', 'summary' => 'Down'], 'Down', 'red'],
    'pending' => [['latest_run_result' => 'pending', 'summary' => null], 'Pending', 'gray'],
    'warning' => [['latest_run_result' => 'warning', 'summary' => 'Slow'], 'Slow', 'amber'],
]);

it('does not request HTTP metrics for non-HTTP monitors', function () {
    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData(['type' => 'tcp'])),
    ]);

    Livewire::test(OhDearUptimePulseCardComponent::class)
        ->assertSee('Up')
        ->assertSee('Response times are available for HTTP monitors');
});

it('renders every page of scheduled tasks without exposing ping secrets', function () {
    MockClient::global()->addResponses([
        MockResponse::make(['data' => [cronData('backup:run', 1)], 'meta' => ['current_page' => 1, 'last_page' => 2]]),
        MockResponse::make(['data' => [cronData('reports:send', 2)], 'meta' => ['current_page' => 2, 'last_page' => 2]]),
    ]);

    Livewire::test(OhDearCronPulseCardComponent::class, ['monitorId' => 456])
        ->assertSee('backup:run')
        ->assertSee('reports:send')
        ->assertSee('Every hour')
        ->assertSeeHtml('https://ohdear.app/monitors/456/check/scheduled-tasks/2')
        ->assertDontSee('secret-ping-url');
});

it('renders every page of broken links, including missing and server error status codes', function () {
    MockClient::global()->addResponses([
        MockResponse::make(['data' => [brokenLinkData()], 'meta' => ['current_page' => 1, 'last_page' => 2]]),
        MockResponse::make(['data' => [brokenLinkData(['status_code' => null, 'link_text' => 'Timeout']), brokenLinkData(['status_code' => 503, 'link_text' => 'Unavailable'])], 'meta' => ['current_page' => 2, 'last_page' => 2]]),
    ]);

    Livewire::test(OhDearBrokenLinksCardComponent::class)
        ->assertSee('404')
        ->assertSee('No response')
        ->assertSee('503')
        ->assertSee('Timeout')
        ->assertSee('Unavailable');
});

it('escapes scraped link text and does not make unsafe URLs clickable', function () {
    MockClient::global()->addResponses([
        MockResponse::make(['data' => [brokenLinkData([
            'crawled_url' => 'javascript:alert(1)',
            'relative_crawled_url' => 'Unsafe link',
            'link_text' => '<script>alert(1)</script>',
        ])], 'meta' => ['current_page' => 1, 'last_page' => 1]]),
    ]);

    Livewire::test(OhDearBrokenLinksCardComponent::class)
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSeeHtml('<script>alert(1)</script>')
        ->assertDontSeeHtml('href="javascript:');
});

it('renders empty tables', function (string $component, string $message) {
    MockClient::global()->addResponses([
        MockResponse::make(['data' => [], 'meta' => ['current_page' => 1, 'last_page' => 1]]),
    ]);

    Livewire::test($component)->assertSee($message);
})->with([
    [OhDearCronPulseCardComponent::class, 'No scheduled tasks'],
    [OhDearBrokenLinksCardComponent::class, 'No broken links reported'],
]);

it('contains API errors to the affected card and recovers on refresh', function (string $component, int $status, string $message) {
    MockClient::global()->addResponses([
        MockResponse::make(['message' => 'sensitive-server-detail'], $status),
        ...($component === OhDearUptimePulseCardComponent::class ? [MockResponse::make(monitorData())] : []),
        MockResponse::make(['data' => [], 'meta' => ['current_page' => 1, 'last_page' => 1]]),
    ]);

    $card = Livewire::test($component)
        ->assertSee($message)
        ->assertDontSee('sensitive-server-detail');

    $card->call('$refresh')->assertDontSee($message);
})->with('cards')->with([
    [401, 'could not authorize'],
    [403, 'could not authorize'],
    [404, 'Monitor not found'],
    [429, 'rate limit was reached'],
    [503, 'Oh Dear is unavailable'],
]);

it('shows a connection error instead of crashing on a timeout', function () {
    MockClient::global()->addResponses([
        MockResponse::make()->throw(fn (PendingRequest $request) => new FatalRequestException(new RuntimeException('Timeout'), $request)),
    ]);

    Livewire::test(OhDearCronPulseCardComponent::class)->assertSee('Could not connect to Oh Dear');
});

it('refreshes cached uptime after thirty seconds without sharing another token’s data', function () {
    MockClient::global()->addResponses([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData()),
        'https://ohdear.app/api/monitors/123/http-uptime-metrics*' => MockResponse::make(['data' => [metricData('2026-11-13 12:00:00', 0.2)]]),
    ]);

    $card = Livewire::test(OhDearUptimePulseCardComponent::class)->assertSee('Latest 200 milliseconds.');

    MockClient::destroyGlobal();
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(monitorData()),
        'https://ohdear.app/api/monitors/123/http-uptime-metrics*' => MockResponse::make(['data' => [metricData('2026-11-13 12:01:00', 0.7)]]),
    ]);

    $card->call('$refresh')->assertSee('Latest 200 milliseconds.');

    $this->travel(31)->seconds();

    $card->call('$refresh')->assertSee('Latest 700 milliseconds.');

    config()->set('services.oh_dear.pulse.api_key', 'different-token');
    MockClient::destroyGlobal();
    MockClient::global([MockResponse::make([], 403)]);

    Livewire::withoutLazyLoading()->test(OhDearUptimePulseCardComponent::class)
        ->assertSee('could not authorize')
        ->assertDontSee('Latest 700 milliseconds.');
});

it('does not allow the browser to change the configured monitor', function (string $component) {
    config()->set('services.oh_dear.pulse.api_key', null);

    Livewire::test($component)->set('monitorId', 999);
})->with('cards')->throws(CannotUpdateLockedPropertyException::class);

function monitorData(array $overrides = []): array
{
    return array_replace([
        'id' => 123,
        'team_id' => 1,
        'type' => 'http',
        'url' => 'https://example.com',
        'sort_url' => 'example.com',
        'checks' => [['id' => 10, 'type' => 'uptime', 'enabled' => true, 'latest_run_result' => 'succeeded', 'summary' => 'Up']],
    ], $overrides);
}

function metricData(string $date, float $seconds): array
{
    return [
        'date' => $date,
        'dns_time_in_seconds' => 0.001,
        'tcp_time_in_seconds' => 0.002,
        'ssl_handshake_time_in_seconds' => 0.003,
        'remote_server_processing_time_in_seconds' => 0.004,
        'download_time_in_seconds' => 0.005,
        'total_time_in_seconds' => $seconds,
    ];
}

function cronData(string $name, int $id): array
{
    return [
        'id' => $id,
        'uuid' => "task-{$id}",
        'name' => $name,
        'type' => 'cron',
        'cron_expression' => '0 * * * *',
        'human_readable_cron_expression' => 'Every hour',
        'ping_url' => 'https://ping.ohdear.app/secret-ping-url',
        'created_at' => '2026-11-13 12:00:00',
        'latest_result' => 'onTime',
        'latest_result_label' => 'On time',
        'latest_result_label_color' => 'green',
    ];
}

function brokenLinkData(array $overrides = []): array
{
    return array_replace([
        'status_code' => 404,
        'crawled_url' => 'https://example.com/missing',
        'relative_crawled_url' => '/missing',
        'found_on_url' => 'https://example.com/docs',
        'relative_found_on_url' => '/docs',
        'link_text' => 'Documentation',
        'internal' => true,
    ], $overrides);
}
