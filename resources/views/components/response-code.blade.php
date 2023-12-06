 @php
    $code = $code ?? 0;

    $color = match(true) {
        Str::startsWith($code, '2') => 'green',
        Str::startsWith($code, '3') => 'blue',
        Str::startsWith($code, '4') => 'orange',
        Str::startsWith($code, '500') => 'red',
        default => 'gray',
    };
@endphp

<x-ohdear-pulse::pill :class="$class ?? ''" :color="$color">{{$code}}</x-ohdear-pulse::pill>