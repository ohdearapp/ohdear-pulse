<?php

namespace OhDear\OhDearPulse\Livewire;

use Illuminate\Support\Collection;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;

#[Lazy]
class OhDearUptimePulseCardComponent extends Card
{
    public Collection $sites;

    public function mount()
    {
        $this->sites = collect();
    }

    public function render()
    {
        return view('ohdear-pulse::uptime');
    }
}
