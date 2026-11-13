<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' ohdear-pulse od:min-w-0'" wire:poll.30s>
    <x-ohdear-pulse::heading title="Uptime" :monitor-id="$monitorId" report="uptime/report">
        <x-ohdear-pulse::logo-uptime />
    </x-ohdear-pulse::heading>

    @if(! $isConfigured)
        <x-ohdear-pulse::message title="Connect your monitor">
            Set your API token and monitor ID in <code>services.oh_dear.pulse</code> to see uptime and response times.
        </x-ohdear-pulse::message>
    @elseif($apiError)
        <x-ohdear-pulse::message title="Unable to load uptime" role="status">{{ $apiError }}</x-ohdear-pulse::message>
    @else
        <div class="od:mt-5 od:flex od:items-center od:justify-between od:gap-3">
            <span class="od:min-w-0 od:truncate od:text-sm od:text-gray-600 od:dark:text-gray-400" title="{{ $monitor->url }}">{{ $monitor->sortUrl }}</span>
            <x-ohdear-pulse::pill :color="$color">{{ $status }}</x-ohdear-pulse::pill>
        </div>

        @if($monitor->type !== 'http')
            <x-ohdear-pulse::message title="Response times are available for HTTP monitors">
                This monitor uses {{ strtoupper($monitor->type) }}. Its uptime status is shown above.
            </x-ohdear-pulse::message>
        @elseif(count($metrics) === 0)
            <x-ohdear-pulse::message title="No response times yet">
                No measurements were recorded in the last 20 minutes. New measurements appear automatically.
            </x-ohdear-pulse::message>
        @else
            @php
                $latest = $metrics[array_key_last($metrics)];
                $first = $metrics[0];
                $maximum = max(1, max(array_column($metrics, 'milliseconds')) * 1.15);
                $duration = max(1, $latest['timestamp'] - $first['timestamp']);
                $points = collect($metrics)->map(function (array $metric) use ($first, $duration, $maximum) {
                    $x = round(($metric['timestamp'] - $first['timestamp']) / $duration * 600, 2);
                    $y = round(100 - $metric['milliseconds'] / $maximum * 90, 2);

                    return "{$x},{$y}";
                })->implode(' ');
            @endphp
            <div class="od:mt-5 od:flex od:items-baseline od:gap-2 od:tabular-nums">
                <span class="od:text-3xl od:font-semibold od:tracking-tight">{{ number_format($latest['milliseconds'], 0) }}<span class="od:ml-1 od:text-base od:font-normal od:text-gray-500 od:dark:text-gray-400">ms</span></span>
                <span class="od:text-xs od:text-gray-500 od:dark:text-gray-400">Latest response</span>
            </div>
            <figure class="od:mt-4">
                <svg viewBox="0 0 600 110" preserveAspectRatio="none" class="od:h-24 od:w-full od:overflow-visible od:text-emerald-600 od:dark:text-emerald-400" role="img" aria-label="HTTP response times over the last 20 minutes, oldest to newest. Latest {{ $latest['milliseconds'] }} milliseconds.">
                    <path d="M0 100 H600" class="od:stroke-gray-200 od:dark:stroke-gray-700" stroke-width="1" vector-effect="non-scaling-stroke" />
                    @if(count($metrics) > 1)
                        <polyline points="{{ $points }}" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" />
                    @else
                        <circle cx="0" cy="{{ round(100 - $latest['milliseconds'] / $maximum * 90, 2) }}" r="3" fill="currentColor" />
                    @endif
                </svg>
                <figcaption class="od:mt-2 od:flex od:justify-between od:gap-2 od:text-xs od:tabular-nums od:text-gray-500 od:dark:text-gray-400">
                    <span>{{ \Carbon\CarbonImmutable::createFromTimestamp($first['timestamp'])->diffForHumans() }}</span>
                    <span>{{ \Carbon\CarbonImmutable::createFromTimestamp($latest['timestamp'])->diffForHumans() }}</span>
                </figcaption>
            </figure>
        @endif
    @endif
</x-pulse::card>
