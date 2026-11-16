<?php

namespace Workbench\App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Symfony\Component\HttpFoundation\Response;

class PreviewCheckCards
{
    public function handle(Request $request, Closure $next): Response
    {
        $scenario = $request->query('scenario');

        if ($scenario === null && Livewire::isLivewireRequest()) {
            parse_str(parse_url($request->headers->get('referer', ''), PHP_URL_QUERY) ?? '', $query);
            $scenario = $query['scenario'] ?? null;
        }

        $scenarios = ['healthy', 'warning', 'failing', 'empty', 'unconfigured', 'api-error', 'loading', 'disabled', 'snoozed', 'pending', 'unsupported', 'unavailable', 'missing-expiry', 'health-statuses'];
        View::share('previewScenarios', $scenarios);
        View::share('previewScenario', $scenario);

        if (! in_array($scenario, $scenarios, true)) {
            return $next($request);
        }

        config()->set('cache.default', 'array');
        config()->set('pulse.cache', 'array');
        config()->set('services.oh_dear.pulse', [
            'api_key' => $scenario === 'unconfigured' ? null : 'preview-not-a-real-token',
            'monitor_id' => 123,
            'domain_monitor_id' => 123,
        ]);

        if ($scenario === 'loading') {
            return $next($request);
        }

        $result = match ($scenario) {
            'warning' => 'warning',
            'failing' => 'failed',
            'pending', 'unsupported' => 'pending',
            default => 'succeeded',
        };
        $checks = array_map(fn (string $type, int $index) => [
            'id' => $index + 1,
            'type' => $type,
            'enabled' => $scenario !== 'disabled',
            'latest_run_result' => $result,
            'summary' => match ($scenario) {
                'warning' => 'Needs attention in Oh Dear',
                'failing' => 'Oh Dear detected a problem',
                'unsupported' => 'Not supported',
                'pending' => null,
                default => 'Ok',
            },
            'latest_run_ended_at' => '2026-11-16 08:20:00',
            'active_snooze' => $scenario === 'snoozed' ? ['ends_at' => '2026-11-17 08:20:00'] : null,
        ], ['certificate_health', 'domain', 'application_health'], [0, 1, 2]);

        $healthChecks = array_map(fn (string $name, int $index) => [
            'id' => $index + 1,
            'name' => $name,
            'label' => $name,
            'status' => match (true) {
                $index !== 0 => 'ok',
                $scenario === 'warning' => 'warning',
                $scenario === 'failing' => 'failed',
                default => 'ok',
            },
            'short_summary' => $index === 0 ? 'Queue latency' : 'Connected',
            'message' => $index === 0 ? (in_array($scenario, ['warning', 'failing'], true) ? 'Oldest waiting job: 8 minutes' : 'No jobs waiting') : null,
        ], ['Queue', 'Database', 'Cache'], [0, 1, 2]);

        if ($scenario === 'health-statuses') {
            $healthChecks = array_map(fn (string $status, int $index) => [
                'id' => $index + 1,
                'name' => 'Check'.$index,
                'label' => ucfirst($status).' check',
                'status' => $status,
                'short_summary' => 'Example result from Laravel Health',
                'active_snooze' => $status === 'snoozed' ? ['ends_at' => '2026-11-17 08:20:00'] : null,
            ], ['ok', 'warning', 'failed', 'crashed', 'skipped', 'pending', 'snoozed'], range(0, 6));
        }

        $expiresAt = match ($scenario) {
            'warning' => '2026-11-20 08:20:00',
            'failing' => '2026-11-15 08:20:00',
            'missing-expiry' => null,
            default => '2027-02-14 08:20:00',
        };

        MockClient::global([
            'https://ohdear.app/api/monitors/123' => MockResponse::make([
                'id' => 123,
                'team_id' => 1,
                'url' => 'https://pulse-preview.example.com',
                'sort_url' => 'pulse-preview.example.com',
                'checks' => $scenario === 'unavailable' ? [] : $checks,
            ]),
            'https://ohdear.app/api/certificate-health/123' => MockResponse::make($scenario === 'empty' ? [] : [
                'certificate_details' => ['issuer' => 'Let’s Encrypt', 'valid_until' => $expiresAt],
                'certificate_checks' => [
                    ['type' => 'notFound', 'label' => 'Certificate is present', 'passed' => true],
                    ['type' => 'expiresSoon', 'label' => 'Will not expire soon', 'passed' => ! in_array($scenario, ['warning', 'failing'], true)],
                    ['type' => 'coversWrongDomain', 'label' => 'Covers the right domain', 'passed' => $scenario !== 'failing'],
                ],
            ], $scenario === 'api-error' ? 503 : 200),
            'https://ohdear.app/api/monitors/123/domain' => MockResponse::make($scenario === 'empty' ? [] : [
                'expires_at' => $expiresAt,
                'registered_at' => '2013-12-28 15:59:33',
                'domain_statuses' => ['client transfer prohibited' => true, 'server delete prohibited' => false],
            ], $scenario === 'api-error' ? 503 : 200),
            'https://ohdear.app/api/monitors/123/application-health-checks' => MockResponse::make([
                'data' => $scenario === 'empty' ? [] : $healthChecks,
            ], $scenario === 'api-error' ? 503 : 200),
        ]);

        return $next($request);
    }
}
