@php
    use Illuminate\Support\Str;
@endphp
<x-pulse::card :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Oh Dear in package"
        title="Custom title"
        details="Custom details"
    >
        <x-slot:icon>
            <x-pulse::icons.queue-list />
        </x-slot:icon>
    </x-pulse::card-header>

    <x-pulse::scroll :expand="$expand" wire:poll.5s="">
        @if ($sites->isEmpty())
            <x-pulse::no-results />
        @else
            <div class="grid gap-3 mx-px mb-px">
                @foreach ($sites as $site)
                    <div wire:key="{{ $site->id }}">
                        <h3 class="font-bold text-gray-700 dark:text-gray-300">
                            Site name
                        </h3>

                        <div class="mt-3 relative">
                            Details of the site
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-pulse::scroll>
</x-pulse::card>
