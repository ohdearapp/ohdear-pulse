<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' ohdear-pulse od:min-w-0'" wire:poll.60s>
    <x-ohdear-pulse::heading title="Broken links" :monitor-id="$monitorId" report="broken-links/report">
        <x-ohdear-pulse::logo-broken-link />
    </x-ohdear-pulse::heading>

    @if(! $isConfigured)
        <x-ohdear-pulse::message title="Connect your monitor">
            Set your API token and monitor ID in <code>services.oh_dear.pulse</code> to see broken links.
        </x-ohdear-pulse::message>
    @elseif($apiError)
        <x-ohdear-pulse::message title="Unable to load broken links" role="status">{{ $apiError }}</x-ohdear-pulse::message>
    @elseif(count($brokenLinks) === 0)
        <x-ohdear-pulse::message title="No broken links reported">
            Oh Dear has no broken links to report for this monitor. Results refresh every 15 minutes.
        </x-ohdear-pulse::message>
    @else
        <div class="od:mt-4 od:max-h-80 od:overflow-auto" tabindex="0" role="region" aria-label="Broken links, scroll for more">
            <table class="od:w-full od:min-w-[40rem] od:table-fixed od:text-left od:text-sm">
                <thead class="od:text-xs od:text-gray-500 od:dark:text-gray-400">
                    <tr>
                        <th scope="col" class="od:w-24 od:pb-3 od:font-medium">HTTP status</th>
                        <th scope="col" class="od:w-1/3 od:pb-3 od:font-medium">Broken link</th>
                        <th scope="col" class="od:w-1/3 od:pb-3 od:font-medium">Found on</th>
                        <th scope="col" class="od:pb-3 od:font-medium">Link text</th>
                    </tr>
                </thead>
                <tbody class="od:divide-y od:divide-gray-100 od:dark:divide-gray-800">
                    @foreach($brokenLinks as $brokenLink)
                        <tr wire:key="broken-{{ $monitorId }}-{{ $loop->index }}">
                            <td class="od:py-3 od:pr-3"><x-ohdear-pulse::response-code :code="$brokenLink->statusCode" /></td>
                            <td class="od:py-1 od:pr-4">
                                <x-ohdear-pulse::link :href="$brokenLink->crawledUrl" :title="$brokenLink->crawledUrl" class="od:max-w-full">
                                    <span class="od:truncate">{{ $brokenLink->relativeCrawledUrl ?: $brokenLink->crawledUrl }}</span>
                                </x-ohdear-pulse::link>
                            </td>
                            <td class="od:py-1 od:pr-4">
                                <x-ohdear-pulse::link :href="$brokenLink->foundOnUrl" :title="$brokenLink->foundOnUrl" class="od:max-w-full">
                                    <span class="od:truncate">{{ $brokenLink->relativeFoundOnUrl ?: $brokenLink->foundOnUrl }}</span>
                                </x-ohdear-pulse::link>
                            </td>
                            <td class="od:truncate od:py-3" title="{{ $brokenLink->linkText }}">{{ $brokenLink->linkText ?: '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="od:mt-2 od:text-xs od:text-gray-500 od:dark:text-gray-400">{{ count($brokenLinks) }} broken {{ count($brokenLinks) === 1 ? 'link' : 'links' }} · Scroll for more</p>
    @endif
</x-pulse::card>
