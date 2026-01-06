<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Resources;

class Check extends ApiResource
{
    public int $id;

    public string $type;

    /*
     * The human-readable version of type.
     */
    public string $label;

    public bool $enabled;

    public string $summary;
}
