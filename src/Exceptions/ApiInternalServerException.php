<?php
namespace OpenFGA\Exceptions;

use Exception;

final class ApiInternalServerException extends Exception
{
    public const string EXCEPTION_MESSAGE = 'API request failed (internal server): %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
