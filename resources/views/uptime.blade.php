@php
    use Illuminate\Support\Str;
@endphp

@push('scripts')
<script>
    console.log("This is the script from the uptime card.");
</script>
@endpush


<x-pulse::card id="ohdear" :cols="$cols" :rows="$rows" :class="$class">
    <div class="absolute top-0 right-0 z-0 w-32 h-32 rounded-tr-lg pattern-wavy pattern-blue-500 pattern-bg-transparent pattern-size-16 pattern-opacity-2"></div>
    <div class="relative flex items-center justify-between space-x-2">
        <div class="absolute top-0 flex justify-center w-full">
            <div class="px-2 text-gray-900 rounded-full bg-emerald-400">online</div>
        </div>

        <div class="flex items-center space-x-2">
            <x-ohdear-pulse::logo>
                <x-ohdear-pulse::logo-uptime  />
            </x-ohdear-pulse::logo>

            <h2 class="text-base font-bold text-gray-600 truncate dark:text-gray-300" title="Broken Links">Uptime</h2>
        </div>

        <div>
            <a href="https://ohdear.app/sites/{{config('services.oh_dear.pulse.site_id')}}/check/uptime/report" target="_blank" class="text-xs font-bold text-gray-700 uppercase transition-colors dark:text-gray-400 dark:hover:text-brand ">
                <x-ohdear-pulse::logo-ohdear class="inline-block w-10" />
            </a>
        </div>
    </div>

    <div class="relative grid h-full grid-cols-1 gap-4 mt-4" id="performance">
        {{-- <div class="flex flex-col items-center justify-center space-y-2 rounded-lg bg-emerald-300 dark:bg-emerald-800 ">
            <span class="text-2xl font-bold text-emerald-900 dark:text-white">{{ $status }}</span>
            <span class="text-xs font-medium tracking-wider uppercase text-emerald-700 dark:text-white/50 ">Status</span>
        </div>
        <div class="flex flex-col items-center justify-center space-y-2 rounded-lg bg-emerald-300 dark:bg-emerald-800">
            <span class="text-2xl font-bold text-emerald-900 dark:text-white">{{ $performance }}</span>
            <span class="text-xs font-medium tracking-wider uppercase text-emerald-700 dark:text-white/50 ">Performance</span>
        </div> --}}
    </div>


        {{-- <div class="flex flex-col gap-6">
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
        </div> --}}
    </div>
</x-pulse::card>
