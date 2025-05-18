<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;

use function sprintf;

final class ApiUnauthenticatedException extends Exception
{
    public const EXCEPTION_MESSAGE = 'API request failed (unauthenticated): %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
