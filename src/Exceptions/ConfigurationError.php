<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Throwable;

enum ConfigurationError: string
{
    case HttpClientMissing = 'http_client_missing';

    case HttpRequestFactoryMissing = 'http_request_factory_missing';

    case HttpResponseFactoryMissing = 'http_response_factory_missing';

    case HttpStreamFactoryMissing = 'http_stream_factory_missing';

    /**
     * Creates and returns a new ConfigurationException.
     *
     * @param ?RequestInterface    $request  The HTTP request that triggered the exception, if applicable.
     * @param ?ResponseInterface   $response The HTTP response received, if applicable.
     * @param array<string, mixed> $context  Additional context for the exception.
     * @param ?Throwable           $prev     The previous throwable used for exception chaining, if any.
     *
     * @return ClientThrowable The newly created ConfigurationException instance.
     */
    public function exception(
        ?RequestInterface $request = null,
        ?ResponseInterface $response = null,
        array $context = [],
        ?Throwable $prev = null,
    ): ClientThrowable {
        return new ConfigurationException($this, $request, $response, $context, $prev);
    }
}
