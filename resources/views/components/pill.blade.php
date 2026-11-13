@props(['color' => 'gray'])

@php
    $colorClasses = match($color) {
        'green' => 'od:bg-emerald-50 od:text-emerald-800 od:dark:bg-emerald-950 od:dark:text-emerald-300',
        'red' => 'od:bg-red-50 od:text-red-800 od:dark:bg-red-950 od:dark:text-red-300',
        'orange', 'amber', 'yellow' => 'od:bg-amber-50 od:text-amber-800 od:dark:bg-amber-950 od:dark:text-amber-300',
        default => 'od:bg-gray-100 od:text-gray-700 od:dark:bg-gray-800 od:dark:text-gray-300',
    };
@endphp

<span {{ $attributes->class(['od:inline-flex od:shrink-0 od:items-center od:rounded-md od:px-2 od:py-1 od:text-xs od:font-medium od:whitespace-nowrap od:tabular-nums', $colorClasses]) }}>{{ $slot }}</span>
