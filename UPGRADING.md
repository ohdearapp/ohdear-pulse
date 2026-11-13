# Upgrading from v1 to v2

Version 2 uses Oh Dear's monitor API. The old sites performance endpoint is no longer available. Upgrade the package and configuration together.

1. Upgrade your application to PHP 8.3 or newer and Laravel 13. Upgrade Laravel Pulse to 1.10 or newer. Applications on Laravel 10, 11, or 12 must upgrade Laravel first. Contributors running this package's Pest 5 test suite need PHP 8.4 or newer; CI tests PHP 8.4 and 8.5.

2. Update the package and SDK, allowing Composer to update related dependencies:

   ```bash
   composer require ohdearapp/ohdear-pulse:^2.0 ohdearapp/ohdear-php-sdk:^4.7 --with-all-dependencies
   ```

   If your application uses the SDK directly, also follow the SDK's v4 upgrade documentation. Version 2 does not bundle the SDK fork from the earlier v2 proposal.

3. In `config/services.php`, replace `site_id` with `monitor_id`:

   ```php
   'oh_dear' => [
       'pulse' => [
           'api_key' => env('OH_DEAR_API_TOKEN'),
           'monitor_id' => env('OH_DEAR_MONITOR_ID'),
       ],
   ],
   ```

   Keep your existing API token if it can read the monitor. Replace `OH_DEAR_SITE_ID` in your environment with `OH_DEAR_MONITOR_ID`. Confirm the ID in Oh Dear's monitor settings or `/monitors/{id}` URL rather than assuming an old site ID still identifies the same monitor.

4. In `resources/views/vendor/pulse/dashboard.blade.php`, replace every `site-id` override with a numeric `monitor-id` override:

   ```blade
   <livewire:ohdear.pulse.uptime cols="4" :monitor-id="123" />
   <livewire:ohdear.pulse.cron cols="8" :monitor-id="123" />
   <livewire:ohdear.pulse.brokenLinks cols="full" :monitor-id="123" />
   ```

   Card names are unchanged. Omit the override to use the configured monitor. If you have published or overridden this package's Blade views, remove those overrides or port them to the new monitor DTOs and templates. The old performance canvas and Chart.js integration are gone; HTTP response times now render as an accessible SVG. Remove custom JavaScript that targets the old canvas.

5. Rebuild configuration and view caches using your normal deployment procedure:

   ```bash
   php artisan config:cache
   php artisan view:clear
   php artisan ohdear-pulse:verify
   ```

   The last command must print `Connected to Oh Dear. Monitor` and the expected monitor URL. A nonzero exit code means configuration or API access needs correcting. Version 2 uses new cache keys, so it does not require flushing your application's shared cache.

6. Open your authorized Pulse dashboard in light and dark mode. Check the monitor URL, uptime state, scheduled tasks, and broken links. Check a per-card monitor override if you use one. Empty metrics, non-HTTP monitors, and API failures now have explicit states rather than throwing exceptions. HTTP response times update every 30 seconds; scheduled tasks every minute; broken-link results are cached for 15 minutes.

No database migration, asset publication, or consumer-side Node build is required. The unused facade alias and `SiteIdNotCorrect` exception were removed; use the Artisan command's exit status for automated configuration checks.
