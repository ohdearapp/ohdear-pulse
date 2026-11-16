@if(! $details->certificateChecks && ! $details->certificateDetails)
    <x-ohdear-pulse::message title="No certificate results yet">Certificate details will appear after Oh Dear checks this monitor.</x-ohdear-pulse::message>
@else
    <div class="od:mt-5">
        <dl class="od:space-y-3 od:text-sm">
            <div class="od:flex od:justify-between od:gap-4">
                <dt class="od:text-gray-500 od:dark:text-gray-400">Issuer</dt>
                <dd class="od:min-w-0 od:break-words od:text-right">{{ $details->getIssuer() ?: 'Not available' }}</dd>
            </div>
            <div class="od:flex od:justify-between od:gap-4">
                <dt class="od:text-gray-500 od:dark:text-gray-400">Valid until</dt>
                <dd class="od:text-right od:tabular-nums">{{ $details->getValidUntil() ? $details->getValidUntil() . ' UTC' : 'Not available' }}</dd>
            </div>
        </dl>
    </div>
    <div class="od:mt-4 od:max-h-60 od:overflow-auto" tabindex="0" role="region" aria-label="Certificate checks, scroll for more">
        <ul class="od:divide-y od:divide-gray-100 od:dark:divide-gray-800">
            @foreach($details->certificateChecks as $certificateCheck)
                <li class="od:flex od:items-start od:justify-between od:gap-3 od:py-2 od:text-sm">
                    <span class="od:min-w-0 od:break-words">{{ $certificateCheck['label'] }}</span>
                    <x-ohdear-pulse::pill :color="($certificateCheck['passed'] ?? false) ? 'green' : 'red'">{{ ($certificateCheck['passed'] ?? false) ? 'Passed' : 'Failed' }}</x-ohdear-pulse::pill>
                </li>
            @endforeach
        </ul>
    </div>
@endif
