<?php

namespace OhDear\OhDearPulse\Enums;

enum Status: string
{
    case Ok = 'ok';
    case Warning = 'warning';
    case Failed = 'failed';
    case Crashed = 'crashed';
}
