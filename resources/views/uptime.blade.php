@php
    use Illuminate\Support\Str;
@endphp

<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <x-pulse::card-header
        name="Uptime"
    >
        <x-slot:icon>
            <x-pulse::icons.rocket-launch/>
        </x-slot:icon>
    </x-pulse::card-header>

    <div>
        <div class="flex flex-col gap-6">
            <div class="grid grid-cols-3 gap-3 text-center">
                <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl font-bold text-gray-700 uppercase dark:text-gray-300 tabular-nums">
                                {{ $status }}
                        </span>
                    <span class="text-xs font-bold text-gray-500 uppercase dark:text-gray-400">
                            Status
                        </span>
                </div>
                <div class="flex flex-col justify-center @sm:block">
                        <span class="text-xl font-bold text-gray-700 uppercase dark:text-gray-300 tabular-nums">
                            {{ $performance }}
                        </span>
                    <span class="text-xs font-bold text-gray-500 uppercase dark:text-gray-400">
                            Performance
                        </span>
                </div>
            </div>
        </div>
    </div>
</x-pulse::card>
