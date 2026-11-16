@if(! $details->expiresAt && ! $details->registeredAt && ! $details->domainStatuses)
    <x-ohdear-pulse::message title="No domain details available">The registry has not provided domain details. Not every registry publishes an expiry date.</x-ohdear-pulse::message>
@else
    <div class="od:mt-5">
        <dl class="od:space-y-4 od:text-sm">
            <div>
                <dt class="od:text-gray-500 od:dark:text-gray-400">Expires at</dt>
                <dd class="od:mt-1 od:break-words od:font-semibold od:tabular-nums">{{ $details->expiresAt ? $details->expiresAt . ' UTC' : 'Not provided by registry' }}</dd>
            </div>
            <div>
                <dt class="od:text-gray-500 od:dark:text-gray-400">Registered at</dt>
                <dd class="od:mt-1 od:tabular-nums">{{ $details->registeredAt ? $details->registeredAt . ' UTC' : 'Not provided by registry' }}</dd>
            </div>
            @if($details->domainStatuses)
                <div>
                    <dt class="od:text-gray-500 od:dark:text-gray-400">Registry status</dt>
                    <dd class="od:mt-1 od:break-words">{{ implode(', ', array_keys(array_filter($details->domainStatuses))) ?: 'None reported' }}</dd>
                </div>
            @endif
        </dl>
    </div>
@endif
