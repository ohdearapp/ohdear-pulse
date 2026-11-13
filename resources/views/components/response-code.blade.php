@props(['code' => null])

<x-ohdear-pulse::pill :color="$code >= 500 ? 'red' : 'amber'">{{ $code ?? 'No response' }}</x-ohdear-pulse::pill>
