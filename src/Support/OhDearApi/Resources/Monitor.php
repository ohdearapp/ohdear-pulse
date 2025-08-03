<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Resources;

class Monitor extends ApiResource
{
    public int $id;

    public string $url;

    /**
     * The checks of a site.
     *
     * @var Check[]
     */
    public array $checks;

    public string $sortUrl;

    public function __construct(array $attributes, $ohDear = null)
    {
        parent::__construct($attributes, $ohDear);

        $this->checks = array_map(function (array $checkAttributes) use ($ohDear) {
            return new Check($checkAttributes, $ohDear);
        }, $this->checks);
    }
}
