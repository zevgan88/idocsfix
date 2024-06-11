<?php

namespace Werk365\IdentityDocuments\Exceptions;

use Exception;

class CouldNotSetService extends Exception
{
    public static function couldNotDetectInterface($interface, $service)
    {
        return new static("Could not detect interface {$interface} on service {$service}.");
    }
}
