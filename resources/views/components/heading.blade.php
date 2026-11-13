@props(['title', 'monitorId', 'report'])

<header class="od:flex od:items-center od:justify-between od:gap-3">
    <div class="od:flex od:min-w-0 od:items-center od:gap-2">
        <span class="od:w-5 od:shrink-0 od:text-gray-400 od:dark:text-gray-500" aria-hidden="true">{{ $slot }}</span>
        <h2 class="od:truncate od:text-base od:font-semibold od:text-gray-700 od:dark:text-gray-200">{{ $title }}</h2>
    </div>
    @if($monitorId)
        <x-ohdear-pulse::link href="https://ohdear.app/monitors/{{ $monitorId }}/check/{{ $report }}" aria-label="View {{ strtolower($title) }} in Oh Dear" class="od:shrink-0 od:gap-1 od:text-xs od:font-medium">
            Oh Dear <span aria-hidden="true">↗</span>
        </x-ohdear-pulse::link>
    @endif
</header>
