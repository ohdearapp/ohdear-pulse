@props(['href'])

@if(\Illuminate\Support\Str::startsWith(strtolower($href), ['https://', 'http://']))
    <a href="{{ $href }}" target="_blank" rel="noopener noreferrer" {{ $attributes->class(['od:inline-flex od:min-h-11 od:items-center od:rounded-sm od:text-gray-700 od:underline-offset-4 od:hover:underline od:focus-visible:outline-2 od:focus-visible:outline-offset-2 od:focus-visible:outline-gray-500 od:dark:text-gray-300']) }}>{{ $slot }}</a>
@else
    <span {{ $attributes }}>{{ $slot }}</span>
@endif
