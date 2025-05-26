<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

enum AuthenticationError: string
{
    case TokenExpired = 'token_expired';

    case TokenInvalid = 'token_invalid';

    /**
     * Creates and returns a new AuthenticationException.
     *
     * @param ?RequestInterface    $request  The HTTP request that triggered the exception, if applicable.
     * @param ?ResponseInterface   $response The HTTP response received, if applicable.
     * @param array<string, mixed> $context  Additional context for the exception.
     * @param ?Throwable           $prev     The previous throwable used for exception chaining, if any.
     *
     * @return ClientThrowable The newly created AuthenticationException instance.
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ClientThrowable {
        return new AuthenticationException($this, $request, $response, $context, $prev);
    }
}
