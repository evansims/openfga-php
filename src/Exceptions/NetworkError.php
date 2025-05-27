<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

enum NetworkError: string
{
    case Conflict = 'http_409';

    case Forbidden = 'http_403';

    case Invalid = 'http_400';

    case Server = 'http_500';

    case Timeout = 'http_422';

    case Unauthenticated = 'http_401';

    case UndefinedEndpoint = 'http_404';

    case Unexpected = 'unexpected';

    /**
     * Creates and returns a new NetworkException.
     *
     * @param ?RequestInterface    $request  The HTTP request that triggered the exception, if applicable.
     * @param ?ResponseInterface   $response The HTTP response received, if applicable.
     * @param array<string, mixed> $context  Additional context for the exception.
     * @param ?Throwable           $prev     The previous throwable used for exception chaining, if any.
     *
     * @return ClientThrowable The newly created NetworkException instance.
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ClientThrowable {
        return new NetworkException($this, $request, $response, $context, $prev);
    }
}
