<?php

namespace OhDear\OhDearPulse\Livewire;

use Illuminate\Support\Collection;
use Laravel\Pulse\Livewire\Card;
use Livewire\Attributes\Lazy;
use OhDear\PhpSdk\OhDear;
use OhDear\PhpSdk\Resources\Site;

#[Lazy]
class OhDearUptimePulseCardComponent extends Card
{
    public int $siteId;

    public function mount(int $siteId = null)
    {
        $this->sites = collect();

        $this->siteId = $siteId;
    }

    public function render()
    {
        $site = $this->ohDear()->site($this->siteId);



        return view('ohdear-pulse::uptime');
    }

    public function fetchData()
    {

    }

    protected function ohDear(): OhDear
    {
        return app('ohdear-pulse');
    }
}
