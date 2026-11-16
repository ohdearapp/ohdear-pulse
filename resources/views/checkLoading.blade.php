<x-pulse::card :cols="$cols" :rows="$rows" :class="$class . ' ohdear-pulse od:min-w-0'">
    <div role="status" aria-busy="true" class="od:min-h-60">
        <h2 class="od:text-base od:font-semibold od:text-gray-700 od:dark:text-gray-200">{{ $title }}</h2>
        <div class="od:mt-8 od:text-sm od:text-gray-500 od:dark:text-gray-400">Loading {{ strtolower($title) }}…</div>
        <div aria-hidden="true" class="od:mt-4 od:h-4 od:w-2/3 od:rounded od:bg-gray-100 od:dark:bg-gray-800"></div>
        <div aria-hidden="true" class="od:mt-3 od:h-4 od:w-1/2 od:rounded od:bg-gray-100 od:dark:bg-gray-800"></div>
    </div>
</x-pulse::card>
