<?php

use Livewire\Livewire;
use OhDear\OhDearPulse\Livewire\OhDearApplicationHealthCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearCertificateHealthCardComponent;
use OhDear\OhDearPulse\Livewire\OhDearDomainCardComponent;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    MockClient::destroyGlobal();
    Livewire::withoutLazyLoading();
});

afterEach(function () {
    MockClient::destroyGlobal();
});

dataset('check cards', [
    'certificate' => [OhDearCertificateHealthCardComponent::class, 'certificate_health', 'certificate-health/123', [
        'certificate_details' => ['issuer' => 'WE1', 'valid_until' => '2027-01-04 13:00:00'],
        'certificate_checks' => [['type' => 'hasExpired', 'label' => 'Has not expired', 'passed' => true]],
    ], 'WE1'],
    'domain' => [OhDearDomainCardComponent::class, 'domain', 'monitors/123/domain', [
        'expires_at' => '2027-05-09 17:03:12',
        'registered_at' => '2013-12-28 15:59:33',
        'domain_statuses' => ['client transfer prohibited' => true, 'server delete prohibited' => false],
    ], '2027-05-09 17:03:12 UTC'],
    'application health' => [OhDearApplicationHealthCardComponent::class, 'application_health', 'monitors/123/application-health-checks', [
        'data' => [['id' => 1, 'name' => 'Database', 'label' => 'Database', 'status' => 'ok', 'short_summary' => 'Connected', 'meta' => ['password' => 'private-metadata']]],
    ], 'Connected'],
]);

it('renders check results through the registered Livewire names without exposing raw API data', function (string $component, string $type, string $endpoint, array $details, string $expected) {
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(checkMonitorData($type)),
        "https://ohdear.app/api/{$endpoint}" => MockResponse::make($details),
    ]);

    $name = match ($type) {
        'certificate_health' => 'ohdear.pulse.certificateHealth',
        'application_health' => 'ohdear.pulse.applicationHealth',
        'domain' => 'ohdear.pulse.domain',
    };
    $report = $type === 'application_health' ? 'application-health/run' : str_replace('_', '-', $type).'/report';

    Livewire::test($name)
        ->assertSee('Healthy')
        ->assertSee($expected)
        ->assertSeeHtml('https://ohdear.app/monitors/123/check/'.$report)
        ->assertSee('2026-11-16 08:20:00 UTC')
        ->assertDontSee('private-metadata')
        ->assertDontSee('server delete prohibited')
        ->assertDontSee('test-token');
})->with('check cards');

it('preserves warning and failure severity from Oh Dear rather than guessing from expiry dates', function (string $component, string $type, string $endpoint, array $details, string $expected, string $result, string $label, string $color) {
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(checkMonitorData($type, ['latest_run_result' => $result, 'summary' => 'Needs attention'])),
        "https://ohdear.app/api/{$endpoint}" => MockResponse::make($details),
    ]);

    Livewire::test($component)->assertSee($label)->assertSee('Needs attention')->assertSee($expected)->assertViewHas('color', $color);
})->with('check cards')->with([
    ['warning', 'Warning', 'amber'],
    ['failed', 'Failing', 'red'],
    ['errored-or-timed-out', 'Failing', 'red'],
]);

it('does not fetch stale details or turn disabled, snoozed and unavailable checks green', function (string $component, string $type, string $endpoint, array $details, string $expected, array $overrides, string $label) {
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(checkMonitorData($type, $overrides)),
    ]);

    Livewire::test($component)->assertSee($label)->assertDontSee($expected)->assertViewHas('color', 'gray');
})->with('check cards')->with([
    [['enabled' => false], 'Disabled'],
    [['active_snooze' => ['ends_at' => '2027-01-01 12:00:00']], 'Snoozed'],
    [['latest_run_result' => 'pending', 'summary' => null], 'Pending'],
    [['latest_run_result' => null, 'summary' => 'Not supported'], 'Not supported'],
    [['latest_run_result' => 'new-unknown-result', 'summary' => null], 'Pending'],
]);

it('explains an absent check without calling its details endpoint', function (string $component) {
    MockClient::global([MockResponse::make(monitorData(['checks' => []]))]);

    Livewire::test($component)->assertSee('Check not available')->assertViewHas('color', 'gray');
})->with([OhDearCertificateHealthCardComponent::class, OhDearDomainCardComponent::class, OhDearApplicationHealthCardComponent::class]);

it('shows empty results without inventing certificate health, expiry dates or application checks', function (string $component, string $type, string $endpoint, array $details, string $expected) {
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(checkMonitorData($type)),
        "https://ohdear.app/api/{$endpoint}" => MockResponse::make(['data' => []]),
    ]);

    $empty = match ($type) {
        'certificate_health' => 'No certificate results yet',
        'domain' => 'No domain details available',
        'application_health' => 'No application health checks',
    };

    Livewire::test($component)->assertSee($empty)->assertDontSee($expected)->assertSee('No results')->assertViewHas('color', 'gray');
})->with('check cards');

it('contains errors from the details endpoint and recovers after the next poll', function (string $component, string $type, string $endpoint, array $details, string $expected, int $status) {
    MockClient::global([
        'https://ohdear.app/api/monitors/123' => MockResponse::make(checkMonitorData($type)),
        "https://ohdear.app/api/{$endpoint}" => MockResponse::make(['message' => 'sensitive-server-detail'], $status),
    ]);

    $card = Livewire::test($component)->assertSee('Unable to load')->assertDontSee('sensitive-server-detail');

    MockClient::destroyGlobal();
    MockClient::global(["https://ohdear.app/api/{$endpoint}" => MockResponse::make($details)]);

    $card->call('$refresh')->assertDontSee('Unable to load')->assertSee($expected);
})->with('check cards')->with([401, 403, 404, 422, 429, 503]);

it('shows all application check statuses and escapes health messages', function () {
    $statuses = ['ok', 'warning', 'failed', 'crashed', 'skipped', null, 'future-status'];
    $checks = array_map(fn ($status, $index) => [
        'id' => $index,
        'name' => 'Check'.$index,
        'label' => 'Check '.$index,
        'status' => $status,
        'short_summary' => '0',
        'message' => '<script>alert(1)</script>',
    ], $statuses, array_keys($statuses));
    $checks[] = array_replace($checks[0], ['id' => 8, 'active_snooze' => ['ends_at' => '2027-01-01']]);
    MockClient::global([
        MockResponse::make(checkMonitorData('application_health')),
        MockResponse::make(['data' => $checks]),
    ]);

    Livewire::test(OhDearApplicationHealthCardComponent::class)
        ->assertSee(['Healthy', 'Warning', 'Failing', 'Skipped', 'Pending', 'Snoozed', '0', '8 application health checks'])
        ->assertViewHas('status', 'Failing')
        ->assertViewHas('color', 'red')
        ->assertSeeHtml('>0</div>')
        ->assertSee('<script>alert(1)</script>')
        ->assertDontSeeHtml('<script>alert(1)</script>');
});

it('renders failed certificate checks and missing validity dates without claiming they passed', function () {
    MockClient::global([
        MockResponse::make(checkMonitorData('certificate_health', ['latest_run_result' => 'failed'])),
        MockResponse::make(['certificate_checks' => [['type' => 'notFound', 'label' => 'Certificate is present', 'passed' => false]]]),
    ]);

    Livewire::test(OhDearCertificateHealthCardComponent::class)
        ->assertSee(['Failing', 'Certificate is present', 'Failed', 'Not available'])
        ->assertDontSee('Passed');
});

it('raises application severity without downgrading a failed monitor or counting snoozed failures', function (string $result, string $status, ?array $snooze, string $expected, string $color) {
    MockClient::global([
        MockResponse::make(checkMonitorData('application_health', ['latest_run_result' => $result])),
        MockResponse::make(['data' => [[
            'id' => 1, 'name' => 'Queue', 'label' => 'Queue', 'status' => $status, 'active_snooze' => $snooze,
        ]]]),
    ]);

    Livewire::test(OhDearApplicationHealthCardComponent::class)->assertViewHas('status', $expected)->assertViewHas('color', $color);
})->with([
    ['succeeded', 'warning', null, 'Warning', 'amber'],
    ['succeeded', 'failed', ['ends_at' => '2027-01-01'], 'Healthy', 'green'],
    ['succeeded', 'crashed', null, 'Failing', 'red'],
    ['failed', 'ok', null, 'Failing', 'red'],
]);

it('does not invent a domain expiry when the registry only provides registration details', function () {
    MockClient::global([
        MockResponse::make(checkMonitorData('domain')),
        MockResponse::make(['registered_at' => '2013-12-28 15:59:33']),
    ]);

    Livewire::test(OhDearDomainCardComponent::class)->assertSee('Not provided by registry')->assertSee('2013-12-28 15:59:33 UTC');
});

function checkMonitorData(string $type, array $check = []): array
{
    return monitorData(['checks' => [array_replace([
        'id' => 20,
        'type' => $type,
        'enabled' => true,
        'latest_run_result' => 'succeeded',
        'latest_run_ended_at' => '2026-11-16 08:20:00',
        'summary' => 'Ok',
    ], $check)]]);
}
