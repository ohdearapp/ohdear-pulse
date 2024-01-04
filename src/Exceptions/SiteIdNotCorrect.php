<?php

namespace OhDear\OhDearPulse\Exceptions;

use Exception;

class SiteIdNotCorrect extends Exception
{
    public static function make(Exception $previous): self
    {
        return new static('Could not find a site with the given site id.', previous: $previous);
    }
}
