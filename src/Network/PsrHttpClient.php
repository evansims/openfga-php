<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Override;
use Psr\Http\Client\{ClientExceptionInterface, ClientInterface};
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use PsrDiscovery\Discover;
use RuntimeException;

/**
 * PSR-18 compliant HTTP client implementation.
 *
 * This implementation wraps any PSR-18 compatible HTTP client,
 * providing automatic discovery if no client is provided. It ensures
 * compatibility with various HTTP client libraries while maintaining
 * a consistent interface for the OpenFGA SDK.
 */
final readonly class PsrHttpClient implements HttpClientInterface
{
    /**
     * The underlying PSR-18 HTTP client.
     */
    private ClientInterface $httpClient;

    /**
     * Create a new PSR HTTP client instance.
     *
     * @param ClientInterface|null $httpClient Optional PSR-18 HTTP client; will use discovery if not provided
     */
    public function __construct(?ClientInterface $httpClient = null)
    {
        $this->httpClient = $httpClient ?? $this->discoverHttpClient();
    }

    /**
     * @inheritDoc
     *
     * @throws ClientExceptionInterface If an error happens during processing the request
     */
    #[Override]
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->httpClient->sendRequest($request);
    }

    /**
     * Discover an available PSR-18 HTTP client.
     *
     * Uses PSR discovery to find an available HTTP client implementation
     * in the current environment.
     *
     * @throws RuntimeException If no PSR-18 client can be discovered
     *
     * @return ClientInterface The discovered HTTP client
     */
    private function discoverHttpClient(): ClientInterface
    {
        $client = Discover::httpClient();

        if (null === $client) {
            throw new RuntimeException('No PSR-18 HTTP client found. Please install a PSR-18 compatible HTTP client package.');
        }

        return $client;
    }
}
