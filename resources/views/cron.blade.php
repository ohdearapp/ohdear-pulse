@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Cron"
    >
        <x-slot:icon>
            <x-pulse::icons.rocket-launch/>
        </x-slot:icon>
    </x-pulse::card-header>

    <div>
        <div class="flex flex-col gap-6">
            <div>
                <x-pulse::table>
                    <colgroup>
                        <col width="100%"/>
                        <col width="0%"/>
                        <col width="0%"/>
                        <col width="0%"/>
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Task</x-pulse::th>
                            <x-pulse::th class="text-right">Last executed at</x-pulse::th>
                            <x-pulse::th class="text-right">Cron expression</x-pulse::th>
                            <x-pulse::th class="text-right whitespace-nowrap">Result</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                    @foreach ($cronChecks as $cronCheck)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="cronCheck.{{ $cronCheck->id }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 truncate dark:text-gray-100"
                                      title="{{ $cronCheck->name }}">
                                    {{ $cronCheck->name }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td class="font-bold text-gray-700 dark:text-gray-300">
                                <div>
                                    {{ $cronCheck->humanReadableLatestPingAt }}
                                </div>
                                <div>
                                    {{ $cronCheck->latestPingAt }}
                                </div>

                            </x-pulse::td>
                            <x-pulse::td class="font-bold text-gray-700 dark:text-gray-300">
                                {{ $cronCheck->humanReadableCronExpression }}
                                {{ $cronCheck->cronExpression }}
                            </x-pulse::td>
                            <x-pulse::td class="font-bold text-gray-700 dark:text-gray-300">
                                <div>
                                    {{ $cronCheck->latestResultLabel }}
                                </div>
                                <div>
                                    Result color: {{ $cronCheck->latestResultLabelColor }}
                                </div>
                            </x-pulse::td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-pulse::table>
            </div>
        </div>
    </div>
</x-pulse::card>
