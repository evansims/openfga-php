<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Exception;
use InvalidArgumentException;
use OpenFGA\Translation\Translator;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

/**
 * Authentication-related exception for the OpenFGA SDK.
 *
 * Thrown when authentication failures occur, such as expired or invalid tokens.
 * Provides detailed context about the authentication failure including the
 * specific error type and any associated HTTP request/response information.
 */
final class AuthenticationException extends Exception implements ClientThrowable
{
    use ExceptionTrait;

    /**
     * Create a new AuthenticationException instance.
     *
     * Constructs an AuthenticationException with the specified error type and context.
     * The exception message is automatically generated from the error type
     * unless a custom message is provided in the context.
     *
     * @param AuthenticationError    $kind     the type of authentication error that occurred
     * @param RequestInterface|null  $request  the HTTP request that triggered the exception, if applicable
     * @param ResponseInterface|null $response the HTTP response received, if applicable
     * @param array<string, mixed>   $context  additional context for the exception
     * @param Throwable|null         $previous the previous throwable used for exception chaining, if any
     *
     * @throws InvalidArgumentException If message translation parameters are invalid
     */
    public function __construct(
        private readonly AuthenticationError $kind,
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

            $message = Translator::trans(DefaultMessages::forAuthenticationError($kind), $parameters);
        }

        parent::__construct($message, 0, $previous);
    }
}
