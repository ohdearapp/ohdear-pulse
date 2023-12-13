<?php

namespace OhDear\OhDearPulse\Checks\Checks;

use Laravel\Pulse\Facades\Pulse;
use OhDear\OhDearPulse\Checks\Check;
use OhDear\OhDearPulse\Checks\Result;
use Spatie\Regex\Regex;
use Symfony\Component\Process\Process;

class UsedDiskSpaceCheck extends Check
{
    protected int $warningThreshold = 70;

    protected int $errorThreshold = 90;

    protected ?string $filesystemName = null;

    public function filesystemName(string $filesystemName): self
    {
        $this->filesystemName = $filesystemName;

        return $this;
    }

    public function warnWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->warningThreshold = $percentage;

        return $this;
    }

    public function failWhenUsedSpaceIsAbovePercentage(int $percentage): self
    {
        $this->errorThreshold = $percentage;

        return $this;
    }

    public function run(): Result
    {
        $diskSpaceUsedPercentage = $this->getDiskUsagePercentage();

        $result = Result::make()
            ->meta(['disk_space_used_percentage' => $diskSpaceUsedPercentage])
            ->shortSummary($diskSpaceUsedPercentage.'%');

        if ($diskSpaceUsedPercentage > $this->errorThreshold) {
            return $result->failed("The disk is almost full ({$diskSpaceUsedPercentage}% used).");
        }

        if ($diskSpaceUsedPercentage > $this->warningThreshold) {
            return $result->warning("The disk is almost full ({$diskSpaceUsedPercentage}% used).");
        }

        return $result->ok();
    }

    protected function getDiskUsagePercentage(): int
    {
        $firstServer = Pulse::values('system')->first();

        $values = json_decode($firstServer->value, flags: JSON_THROW_ON_ERROR);

        dd($values);
    }
}
