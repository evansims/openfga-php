<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;

use function sprintf;

final class ApiUnexpectedResponseException extends Exception
{
    public const string EXCEPTION_MESSAGE = 'API response was unexpected and unprocessable: %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
