@props(['title'])

<div {{ $attributes->class(['od:flex od:min-h-44 od:flex-1 od:flex-col od:items-center od:justify-center od:gap-2 od:px-4 od:py-8 od:text-center']) }}>
    <p class="od:text-sm od:font-medium od:text-gray-700 od:dark:text-gray-200">{{ $title }}</p>
    <p class="od:max-w-prose od:text-sm od:leading-relaxed od:text-gray-500 od:dark:text-gray-400">{{ $slot }}</p>
</div>
