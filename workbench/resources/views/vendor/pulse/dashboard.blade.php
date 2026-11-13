<x-pulse>
    <livewire:ohdear.pulse.uptime cols="4" />
    <livewire:ohdear.pulse.cron cols="8" />
    <livewire:ohdear.pulse.brokenLinks cols="full" :monitor-id="config('services.oh_dear.pulse.broken_links_monitor_id')" />
</x-pulse>
