@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <div class="flex items-center justify-between space-x-2">
        <div class="flex items-center space-x-2">
            <div class="text-gray-400 dark:text-gray-600">
                <svg width="24px" height="24px" viewBox="0 0 24 24" stroke-width="1.5" fill="none" xmlns="http://www.w3.org/2000/svg" color="currentColor"><path d="M7.14286 16.9953C6.75006 16.9953 6.36756 16.9525 6 16.8715C3.70973 16.3665 2 14.3761 2 11.9977C2 9.284 4.22573 7.07548 7 7.00195" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3184 9.63429C12.7858 8.73635 11.9737 7.96977 11 7.4989" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M16.8571 6.99999C17.2499 6.99999 17.6324 7.04278 18 7.12383C20.2903 7.62884 22 9.6192 22 11.9976C22 14.7577 19.6975 16.9952 16.8571 16.9952C16.581 16.9952 15.4776 16.9952 15.1429 16.9952C12.317 16.9952 10 14.4893 10 11.9976C10 11.9976 10 11 10.5 10.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path><path d="M3 3L21 21" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path></svg>
            </div>
            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Broken Links</h2>
        </div>

        <div>
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/broken-links/report" target="_blank" class="text-xs font-bold text-gray-700 uppercase transition-colors dark:text-gray-400 dark:hover:text-brand ">
                <x-ohdear-pulse::logo class="inline-block w-10" />
            </a>
        </div>

    </div>

    <div class="mt-2">
        <div class="flex flex-col gap-6">
            <div>
                <x-pulse::table class="w-full table-fixed">
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th class="2xl:w-[7%] xl:w-[8%]  text-center truncate whitespace-nowrap">Status code</x-pulse::th>
                            <x-pulse::th class="w-[35%]"> Broken Link</x-pulse::th>
                            <x-pulse::th class="w-[35%]">Found on</x-pulse::th>
                            <x-pulse::th class="w-[20%]">Link text</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                    @foreach ($brokenLinks as $brokenLink)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="brokenLink.{{ md5($brokenLink->crawledUrl) }}">
                            <x-pulse::td class="px-4 ">
                                <x-ohdear-pulse::response-code :code="$brokenLink->statusCode">
                                    {{$brokenLink->statusCode}}
                                </x-ohdear-pulse::response-code>
                            </x-pulse::td>
                            <x-pulse::td class="w-[29%]  text-gray-700 dark:text-gray-300">
                                <div class="w-full truncate">
                                     <a target="_blank" href="{{ $brokenLink->crawledUrl  }}">{{ $brokenLink->relativeCrawledUrl }}</a>
                                </div>
                            </x-pulse::td>
                            <x-pulse::td class="max-w-[25%] text-gray-700 truncate dark:text-gray-300">
                                <a target="_blank"
                                   href="{{ $brokenLink->foundOnUrl  }}">{{ $brokenLink->foundOnUrl }}</a>
                            </x-pulse::td>
                            <x-pulse::td class="text-gray-700 dark:text-gray-300">
                                {{ $brokenLink->linkText }}
                            </x-pulse::td>
                        </tr>
                    @endforeach
                    </tbody>
                </x-pulse::table>
            </div>
        </div>
    </div>
</x-pulse::card>
