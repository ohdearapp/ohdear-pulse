<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Resources;

use OhDear\OhDearPulse\Support\OhDearApi\OhDear;
use ReflectionObject;
use ReflectionProperty;
use stdClass;

class ApiResource extends stdClass
{
    public array $attributes = [];

    protected ?OhDear $ohDear;

    public function __construct(array $attributes, ?OhDear $ohDear = null)
    {
        $this->attributes = $attributes;

        $this->ohDear = $ohDear;

        $this->fill();
    }

    protected function fill(): void
    {
        foreach ($this->attributes as $key => $value) {
            $key = $this->camelCase($key);

            $this->{$key} = $value;
        }
    }

    protected function camelCase(string $key): string
    {
        $parts = explode('_', $key);

        foreach ($parts as $i => $part) {
            if ($i !== 0) {
                $parts[$i] = ucfirst($part);
            }
        }

        return str_replace(' ', '', implode(' ', $parts));
    }

    public function __sleep()
    {
        $publicProperties = (new ReflectionObject($this))->getProperties(ReflectionProperty::IS_PUBLIC);

        $publicPropertyNames = array_map(function (ReflectionProperty $property) {
            return $property->getName();
        }, $publicProperties);

        return array_diff($publicPropertyNames, ['ohDear', 'attributes']);
    }
}
