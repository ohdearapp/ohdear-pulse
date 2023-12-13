<?php

namespace OhDear\OhDearPulse\Checks;

use Illuminate\Support\Str;

abstract class Check
{
    protected ?string $name = null;

    protected ?string $label = null;

    public static function new(): static
    {
        return app(static::class);
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function getLabel(): string
    {
        if ($this->label) {
            return $this->label;
        }

        $name = $this->getName();

        return Str::of($name)->snake()->replace('_', ' ')->title();
    }

    public function getName(): string
    {
        if ($this->name) {
            return $this->name;
        }

        $baseName = class_basename(static::class);

        return Str::of($baseName)->beforeLast('Check');
    }

    abstract public function run(): Result;


}
