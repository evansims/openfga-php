<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

/**
 * HTTP client interface for sending HTTP requests.
 *
 * This interface abstracts the HTTP client implementation, allowing
 * different HTTP clients to be used interchangeably. It follows the
 * PSR-18 HTTP Client standard for compatibility.
 */
interface HttpClientInterface
{
    /**
     * Send an HTTP request and return the response.
     *
     * @param RequestInterface $request The HTTP request to send
     *
     * @throws ClientExceptionInterface If an error happens during processing the request
     *
     * @return ResponseInterface The HTTP response
     */
    public function send(RequestInterface $request): ResponseInterface;
}
