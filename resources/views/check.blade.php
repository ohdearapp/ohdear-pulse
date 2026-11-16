<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' ohdear-pulse od:min-w-0'" wire:poll.60s>
    <x-ohdear-pulse::heading :title="$title" :monitor-id="$monitorId" :report="$report">
        <x-ohdear-pulse::logo-uptime />
    </x-ohdear-pulse::heading>

    @if(! $isConfigured)
        <x-ohdear-pulse::message title="Connect your monitor">
            Set your API token and monitor ID in <code>services.oh_dear.pulse</code> to see {{ strtolower($title) }}.
        </x-ohdear-pulse::message>
    @elseif($apiError)
        <x-ohdear-pulse::message :title="'Unable to load ' . strtolower($title)" role="status">{{ $apiError }}</x-ohdear-pulse::message>
    @else
        <div class="od:mt-5 od:flex od:items-center od:justify-between od:gap-3">
            <span class="od:min-w-0 od:truncate od:text-sm od:text-gray-600 od:dark:text-gray-400" title="{{ $monitor->url }}">{{ $monitor->sortUrl }}</span>
            <x-ohdear-pulse::pill :color="$color">{{ $status }}</x-ohdear-pulse::pill>
        </div>

        @if(! $canLoadDetails)
            <x-ohdear-pulse::message :title="$check ? $status : 'Check not available'">
                @if(! $check || ! $check->enabled)
                    Enable {{ strtolower($title) }} for this monitor in Oh Dear to see results.
                @elseif($check->activeSnooze)
                    Notifications are snoozed in Oh Dear. Results will return when the snooze ends.
                @else
                    No result is available. Oh Dear may still be checking this monitor, or this check may not be supported.
                @endif
            </x-ohdear-pulse::message>
        @else
            @if($check->checkResult()?->isWarning() || $check->checkResult()?->isDown())
                <div class="od:mt-3 od:break-words od:text-sm od:text-gray-600 od:dark:text-gray-400">{{ $check->summary }}</div>
            @endif
            @include('ohdear-pulse::' . $detailsView)
            @if($check->latestRunEndedAt)
                <div class="od:mt-4 od:text-xs od:tabular-nums od:text-gray-500 od:dark:text-gray-400">Checked {{ $check->latestRunEndedAt }} UTC</div>
            @endif
        @endif
    @endif
</x-pulse::card>
