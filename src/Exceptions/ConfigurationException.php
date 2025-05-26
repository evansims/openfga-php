<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use RuntimeException;
use Throwable;

final class ConfigurationException extends RuntimeException implements ClientThrowable
{
    use ExceptionTrait;

    /**
     * Creates a new ConfigurationException.
     *
     * @param ConfigurationError   $kind     The type of configuration error that occurred.
     * @param ?RequestInterface    $request  The HTTP request that triggered the exception, if applicable.
     * @param ?ResponseInterface   $response The HTTP response received, if applicable.
     * @param array<string, mixed> $context  Additional context for the exception.
     * @param ?Throwable           $previous The previous throwable used for exception chaining, if any.
     */
    public function __construct(
        private readonly ConfigurationError $kind,
        private readonly ?RequestInterface $request = null,
        private readonly ?ResponseInterface $response = null,
        private readonly array $context = [],
        private readonly ?Throwable $previous = null,
    ) {
        /** @var string $message */
        $message = $context['message'] ?? '';

        parent::__construct($message, 0, $previous);
    }
}
