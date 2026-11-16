<x-pulse>
    @if($previewScenarios ?? [])
        <div class="od:text-sm od:text-gray-700 od:dark:text-gray-200" style="grid-column: 1 / -1">
            <nav aria-label="Card preview states" style="display: flex; flex-wrap: wrap; gap: 0 16px">
                <x-ohdear-pulse::link href="/pulse">Live API</x-ohdear-pulse::link>
                @foreach($previewScenarios as $scenario)
                    <x-ohdear-pulse::link :href="'?scenario=' . $scenario" :aria-current="($previewScenario ?? null) === $scenario ? 'page' : null">{{ ucfirst(str_replace('-', ' ', $scenario)) }}</x-ohdear-pulse::link>
                @endforeach
            </nav>
            <p>{{ ($previewScenario ?? null) ? 'HTTP fixtures · ' . ucfirst(str_replace('-', ' ', $previewScenario)) . ' · No production monitors changed' : 'Read-only live Oh Dear API data' }}</p>
        </div>
    @endif
    @if(($previewScenario ?? null) === 'loading')
        @foreach(['Certificate health', 'Domain expiry', 'Application health'] as $title)
            @include('ohdear-pulse::checkLoading', ['title' => $title, 'cols' => 4, 'rows' => 1, 'class' => ''])
        @endforeach
    @else
        <livewire:ohdear.pulse.certificateHealth cols="4" />
        <livewire:ohdear.pulse.domain cols="4" :monitor-id="config('services.oh_dear.pulse.domain_monitor_id')" />
        <livewire:ohdear.pulse.applicationHealth cols="4" />
    @endif
    @if(! ($previewScenario ?? null))
        <livewire:ohdear.pulse.uptime cols="4" />
        <livewire:ohdear.pulse.cron cols="8" />
        <livewire:ohdear.pulse.brokenLinks cols="full" :monitor-id="config('services.oh_dear.pulse.broken_links_monitor_id')" />
    @endif
</x-pulse>
