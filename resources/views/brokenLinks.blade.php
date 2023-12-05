@php
    use Illuminate\Support\Str;
@endphp
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Broken Links"
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
                        <col/>
                        <col/>
                        <col/>
                        <col/>
                    </colgroup>
                    <x-pulse::thead>
                        <tr>
                            <x-pulse::th>Status code</x-pulse::th>
                            <x-pulse::th class="text-right">Broken Link</x-pulse::th>
                            <x-pulse::th class="text-right">Found on</x-pulse::th>
                            <x-pulse::th class="text-right whitespace-nowrap">Link text</x-pulse::th>
                        </tr>
                    </x-pulse::thead>
                    <tbody>
                    @foreach ($brokenLinks as $brokenLink)
                        <tr class="h-2 first:h-0"></tr>
                        <tr wire:key="brokenLink.{{ md5($brokenLink->crawledUrl) }}">
                            <x-pulse::td class="max-w-[1px]">
                                <code class="block text-xs text-gray-900 dark:text-gray-100 truncate">
                                    {{ $brokenLink->statusCode }}
                                </code>
                            </x-pulse::td>
                            <x-pulse::td class="text-gray-700 dark:text-gray-300 font-bold">
                                <a target="_blank"
                                   href="{{ $brokenLink->crawledUrl  }}">{{ $brokenLink->relativeCrawledUrl }}</a>

                            </x-pulse::td>
                            <x-pulse::td class="text-gray-700 dark:text-gray-300 font-bold">
                                <a target="_blank"
                                   href="{{ $brokenLink->foundOnUrl  }}">{{ $brokenLink->foundOnUrl }}</a>
                            </x-pulse::td>
                            <x-pulse::td class="text-gray-700 dark:text-gray-300 font-bold">
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
