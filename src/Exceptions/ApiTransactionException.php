<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;

use function sprintf;

final class ApiTransactionException extends Exception
{
    public const string EXCEPTION_MESSAGE = 'API request failed (transaction): %s';

    public function __construct(string $message)
    {
        parent::__construct(sprintf(self::EXCEPTION_MESSAGE, $message));
    }
}
