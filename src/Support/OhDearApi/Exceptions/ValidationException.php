<?php

namespace OhDear\OhDearPulse\Support\OhDearApi\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public array $errors = [];

    public function __construct(array $errors)
    {
        $this->errors = $errors;

        parent::__construct('The given data failed to pass validation. '.print_r($this->errors, true));
    }
}
