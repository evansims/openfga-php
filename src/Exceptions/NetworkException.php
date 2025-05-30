<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;
use InvalidArgumentException;
use OpenFGA\Translation\Translator;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Network-related exception for the OpenFGA SDK.
 *
 * Thrown when network or HTTP communication errors occur while interacting
 * with the OpenFGA API. Includes specific HTTP status code errors, request
 * failures, timeouts, and other network-related issues. Provides access to
 * both the HTTP request and response for detailed debugging.
 */
final class NetworkException extends Exception implements ClientThrowable
{
    use ExceptionTrait;

    /**
     * Create a new NetworkException instance.
     *
     * Constructs a NetworkException with the specified error type and context.
     * The exception message is automatically generated from the error type
     * unless a custom message is provided in the context.
     *
     * @param NetworkError           $kind     The type of network error that occurred.
     * @param RequestInterface|null  $request  The HTTP request that triggered the exception, if applicable.
     * @param ResponseInterface|null $response The HTTP response received, if applicable.
     * @param array<string, mixed>   $context  Additional context for the exception.
     * @param Throwable|null         $previous The previous throwable used for exception chaining, if any.
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     */
    public function __construct(
        private readonly NetworkError $kind,
        private readonly ?RequestInterface $request = null,
        private readonly ?ResponseInterface $response = null,
        private readonly array $context = [],
        private readonly ?Throwable $previous = null,
    ) {
        /** @var string $message */
        $message = $context['message'] ?? '';

        // If no message was provided, use the default message for this error kind
        if ('' === $message) {
            // Remove 'message' from context to avoid circular reference
            $parameters = $context;
            unset($parameters['message']);

            $message = Translator::trans(DefaultMessages::forNetworkError($kind), $parameters);
        }

        parent::__construct($message, 0, $previous);
    }
}
