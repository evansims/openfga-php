<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;
use Throwable;

final class SchemaValidationException extends Exception
{
    /**
     * @param array<string> $errors
     * @param string        $message
     * @param int           $code
     * @param ?Throwable    $previous
     */
    public function __construct(
        private readonly array $errors,
        string $message = 'Validation failed',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return array<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
