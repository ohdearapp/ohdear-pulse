@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <div class="flex items-center justify-between space-x-2">
        <div class="flex items-center space-x-2">
            <x-ohdear-pulse::logo>
                <x-ohdear-pulse::logo-broken-link />
            </x-ohdear-pulse::logo>

            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Broken Links</h2>
        </div>

        <div>
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/broken-links/report" target="_blank" class="text-xs font-bold text-gray-700 uppercase transition-colors dark:text-gray-400 dark:hover:text-brand ">
                <x-ohdear-pulse::logo-ohdear class="inline-block w-10" />
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
                            <x-pulse::td class="text-gray-700 truncate dark:text-gray-300">
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
