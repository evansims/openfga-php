<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;

use function sprintf;

final class ApiInternalServerException extends Exception
{
    public const EXCEPTION_MESSAGE = 'API request failed (internal server): %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
