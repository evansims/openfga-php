<?php
namespace OpenFGA\Exceptions;

use Exception;

final class ApiForbiddenException extends Exception
{
    public const string EXCEPTION_MESSAGE = 'API request failed (forbidden): %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
