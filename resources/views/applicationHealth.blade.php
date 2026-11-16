@if(count($details) === 0)
    <x-ohdear-pulse::message title="No application health checks">Send application health results to this monitor in Oh Dear to see your checks here.</x-ohdear-pulse::message>
@else
    <div class="od:mt-4 od:max-h-80 od:overflow-auto" tabindex="0" role="region" aria-label="Application health checks, scroll for more">
        <ul class="od:divide-y od:divide-gray-100 od:dark:divide-gray-800">
            @foreach($details as $healthCheck)
                @php
                    $healthStatus = $healthCheck->activeSnooze ? 'Snoozed' : match($healthCheck->status) {
                        'ok' => 'Healthy',
                        'warning' => 'Warning',
                        'failed', 'crashed' => 'Failing',
                        'skipped' => 'Skipped',
                        default => 'Pending',
                    };
                    $healthColor = match($healthStatus) {
                        'Healthy' => 'green',
                        'Warning' => 'amber',
                        'Failing' => 'red',
                        default => 'gray',
                    };
                @endphp
                <li class="od:flex od:items-start od:justify-between od:gap-3 od:py-3 od:text-sm" wire:key="health-{{ $monitorId }}-{{ $healthCheck->id }}">
                    <div class="od:min-w-0">
                        <p class="od:break-words od:font-medium">{{ $healthCheck->label }}</p>
                        @if($healthCheck->shortSummary !== null && $healthCheck->shortSummary !== '')
                            <div class="od:mt-1 od:break-words od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ $healthCheck->shortSummary }}</div>
                        @endif
                        @if($healthCheck->message)
                            <div class="od:mt-1 od:break-words od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ $healthCheck->message }}</div>
                        @endif
                    </div>
                    <x-ohdear-pulse::pill :color="$healthColor">{{ $healthStatus }}</x-ohdear-pulse::pill>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="od:mt-2 od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ count($details) }} application health {{ count($details) === 1 ? 'check' : 'checks' }}</div>
@endif
