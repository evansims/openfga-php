<?php

declare(strict_types=1);

namespace OpenFGA\Services;

use OpenFGA\Exceptions\{ClientThrowable, NetworkException};
use OpenFGA\Requests\RequestInterface;
use Psr\Http\Message\{RequestInterface as HttpRequestInterface, ResponseInterface as HttpResponseInterface};

/**
 * Service for handling HTTP communication.
 *
 * This service abstracts HTTP operations from the Client class,
 * providing a clean interface for sending requests and managing
 * HTTP-related state like last request/response tracking.
 */
interface HttpServiceInterface
{
    /**
     * Get the last HTTP request sent.
     *
     * Returns the most recent HTTP request sent by this service,
     * useful for debugging and error reporting.
     *
     * @return HttpRequestInterface|null The last request, or null if no requests sent
     */
    public function getLastRequest(): ?HttpRequestInterface;

    /**
     * Get the last HTTP response received.
     *
     * Returns the most recent HTTP response received by this service,
     * useful for debugging and error reporting.
     *
     * @return HttpResponseInterface|null The last response, or null if no responses received
     */
    public function getLastResponse(): ?HttpResponseInterface;

    /**
     * Send an HTTP request.
     *
     * Sends a request to the OpenFGA API and returns the response.
     * This method handles all HTTP-level concerns including authentication,
     * retries, and error handling.
     *
     * @param RequestInterface $request The OpenFGA request to send
     *
     * @throws NetworkException For network-related errors
     * @throws ClientThrowable  For other client errors
     *
     * @return HttpResponseInterface The HTTP response
     */
    public function send(RequestInterface $request): HttpResponseInterface;
}
