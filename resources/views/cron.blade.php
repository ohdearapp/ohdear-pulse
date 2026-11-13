<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' ohdear-pulse od:min-w-0'" wire:poll.60s>
    <x-ohdear-pulse::heading title="Scheduled tasks" :monitor-id="$monitorId" report="scheduled-tasks/list">
        <x-ohdear-pulse::logo-cron />
    </x-ohdear-pulse::heading>

    @if(! $isConfigured)
        <x-ohdear-pulse::message title="Connect your monitor">
            Set your API token and monitor ID in <code>services.oh_dear.pulse</code> to see scheduled tasks.
        </x-ohdear-pulse::message>
    @elseif($apiError)
        <x-ohdear-pulse::message title="Unable to load scheduled tasks" role="status">{{ $apiError }}</x-ohdear-pulse::message>
    @elseif(count($cronChecks) === 0)
        <x-ohdear-pulse::message title="No scheduled tasks">
            Add cron checks to this monitor in Oh Dear to track when your tasks run.
        </x-ohdear-pulse::message>
    @else
        <div class="od:mt-4 od:max-h-80 od:overflow-auto" tabindex="0" role="region" aria-label="Scheduled tasks, scroll for more">
            <table class="od:w-full od:min-w-[36rem] od:table-fixed od:text-left od:text-sm">
                <thead class="od:text-xs od:text-gray-500 od:dark:text-gray-400">
                    <tr>
                        <th scope="col" class="od:w-2/5 od:pb-3 od:font-medium">Task / schedule</th>
                        <th scope="col" class="od:w-2/5 od:pb-3 od:font-medium">Last ping</th>
                        <th scope="col" class="od:pb-3 od:text-right od:font-medium">Result</th>
                    </tr>
                </thead>
                <tbody class="od:divide-y od:divide-gray-100 od:dark:divide-gray-800">
                    @foreach($cronChecks as $cronCheck)
                        <tr wire:key="cron-{{ $monitorId }}-{{ $cronCheck->id }}">
                            <td class="od:py-2 od:pr-4 od:align-top">
                                <x-ohdear-pulse::link href="https://ohdear.app/monitors/{{ $monitorId }}/check/scheduled-tasks/{{ $cronCheck->id }}" class="od:max-w-full od:font-mono od:text-xs" :title="$cronCheck->name">
                                    <span class="od:truncate">{{ $cronCheck->name }}</span>
                                </x-ohdear-pulse::link>
                                <p class="od:text-xs od:text-gray-500 od:dark:text-gray-400" title="{{ $cronCheck->cronExpression }}">{{ $cronCheck->humanReadableCronExpression ?: ($cronCheck->frequencyInMinutes . ' minute interval') }}</p>
                            </td>
                            <td class="od:py-3 od:pr-4 od:align-top od:tabular-nums">
                                <p>{{ $cronCheck->humanReadableLatestPingAt ?: 'Never received' }}</p>
                                @if($cronCheck->latestPingAt)
                                    <p class="od:mt-1 od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ $cronCheck->latestPingAt }} UTC</p>
                                @endif
                            </td>
                            <td class="od:py-3 od:text-right od:align-top">
                                <x-ohdear-pulse::pill :color="$cronCheck->activeSnooze ? 'gray' : $cronCheck->latestResultLabelColor">{{ $cronCheck->activeSnooze ? 'Snoozed' : ($cronCheck->latestResultLabel ?: 'Pending') }}</x-ohdear-pulse::pill>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="od:mt-2 od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ count($cronChecks) }} scheduled {{ count($cronChecks) === 1 ? 'task' : 'tasks' }} · Scroll for more</p>
    @endif
</x-pulse::card>
