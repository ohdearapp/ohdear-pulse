@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <div class="flex items-center justify-between space-x-2">
        <div class="flex items-center space-x-2">
            <div class="text-gray-400 dark:text-gray-600">
                <svg width="24px" height="24px" stroke-width="1.5" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor"><path d="M12 6L12 12L18 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </div>
            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Cron</h2>
        </div>

        <div>
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/broken-links/report" target="_blank" class="text-xs font-bold text-gray-700 uppercase transition-colors dark:text-gray-400 dark:hover:text-brand ">
                <x-ohdear-pulse::logo class="inline-block w-10" />
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-6 mt-4">
        <div>
            <x-pulse::table class="table-fixed">
                <x-pulse::thead>
                    <tr>
                        <x-pulse::th class="px-4 w-[40%]">Task</x-pulse::th>
                        <x-pulse::th class="w-[25%]">Last executed at</x-pulse::th>
                        <x-pulse::th class="w-[25%]">Cron expression</x-pulse::th>
                        <x-pulse::th class="px-4 w-[10%] text-right">Result</x-pulse::th>
                    </tr>
                </x-pulse::thead>
                <tbody>
                @foreach ($cronChecks as $cronCheck)
                    <tr class="h-2 first:h-0"></tr>
                    <tr wire:key="cronCheck.{{ $cronCheck->id }}">
                        <x-pulse::td class="px-4">
                            <code class="block text-xs text-gray-900 truncate dark:text-gray-100"
                                    title="{{ $cronCheck->name }}">
                                {{ $cronCheck->name }}
                            </code>
                        </x-pulse::td>
                        <x-pulse::td class="">
                            <div class="flex flex-col text-gray-700 dark:text-gray-300">
                                <div>
                                    {{ $cronCheck->humanReadableLatestPingAt }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                    {{ $cronCheck->latestPingAt }}
                                </div>
                            </div>
                        </x-pulse::td>
                        <x-pulse::td class="text-gray-700 dark:text-gray-300">
                            <div class="flex flex-col text-gray-700 dark:text-gray-300">
                                <div>
                                   {{ $cronCheck->humanReadableCronExpression }}
                                </div>
                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                      {{ $cronCheck->cronExpression }}
                                </div>
                            </div>
                        </x-pulse::td>
                        <x-pulse::td class="px-4 text-right text-gray-700 dark:text-gray-300">
                            <x-ohdear-pulse::pill :class="$class ?? ''" :color="$cronCheck->latestResultLabelColor">{{ $cronCheck->latestResultLabel }}</x-ohdear-pulse::pill>
                        </x-pulse::td>
                    </tr>
                @endforeach
                </tbody>
            </x-pulse::table>
        </div>
    </div>

</x-pulse::card>
