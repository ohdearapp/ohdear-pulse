# Integrate Oh Dear with Laravel Pulse

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ohdearapp/ohdear-pulse.svg?style=flat-square)](https://packagist.org/packages/ohdearapp/ohdear-pulse)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/ohdearapp/ohdear-pulse/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/ohdearapp/ohdear-pulse/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/ohdearapp/ohdear-pulse/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/ohdearapp/ohdear-pulse/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/ohdearapp/ohdear-pulse.svg?style=flat-square)](https://packagist.org/packages/ohdearapp/ohdear-pulse)

This package contains Pulse cards that show results from [Oh Dear](https://ohdear.app) in your [Laravel Pulse](https://pulse.laravel.com) dashboard.

Currently, there are three cards available:

- [Uptime and Performance](https://ohdear.app/docs/features/uptime-monitoring)

<img src=https://github.com/ohdearapp/ohdear-pulse/blob/main/docs/uptime.png?raw=true" height=100 />

- [Cron Job Monitoring](https://ohdear.app/docs/features/cron-job-monitoring)

![screenshot](https://github.com/ohdearapp/ohdear-pulse/blob/main/docs/cron.png?raw=true)

- [Broken links](https://ohdear.app/docs/features/broken-links-detection)

![screenshot](https://github.com/ohdearapp/ohdear-pulse/blob/main/docs/broken-links.png?raw=true)

## Installation

You can install the package via composer:

```bash
composer require ohdearapp/ohdear-pulse
```

In your `config/services.php` file, add the following lines:

```php
'oh_dear' => [
    'pulse' => [
        'api_key' => env('OH_DEAR_API_TOKEN'),
        'site_id' => env('OH_DEAR_SITE_ID'),
    ],
],
```

You can create an API token on the "API Tokens" page at Oh Dear. You'll find the site ID on the "Settings" page of a site on Oh Dear.

## Usage

There are currently three cards available:

- `ohdear.pulse.uptime`: displays the uptime and performance of a site
- `ohdear.pulse.cron`: displays the results of the cron job monitoring
- `ohdear.pulse.brokenLinks`: displays any broken links that were detected

You can add the cards to your Pulse dashboard, by first publishing the Pulse's dashboard view:

```bash
php artisan vendor:publish --tag=pulse-dashboard
```

Next, add the cards to the `resources/views/vendor/pulse/dashboard.blade.php` file:

```html
<x-pulse>
    <livewire:ohdear.pulse.uptime cols="4" />
    
    <livewire:ohdear.pulse.cron cols="8" />

    <livewire:ohdear.pulse.brokenLinks cols="8" />
    
    {{-- Add more cards here --}}
</x-pulse>
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
