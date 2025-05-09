<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use Exception;
use OpenFGA\Client;
use OpenFGA\RequestOptions\RequestOptionsInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface as PsrRequestFactoryInterface, RequestInterface as PsrRequestInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};

use PsrDiscovery\Discover;

use function sprintf;

final class RequestFactory implements RequestFactoryInterface
{
    private ?PsrRequestInterface $lastRequest = null;

    private ?ResponseInterface $lastResponse = null;

    public function __construct(
        private string $apiUrl,
        private ?string $authorizationHeader = null,
        private ?ClientInterface $httpClient = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
        private ?PsrRequestFactoryInterface $httpRequestFactory = null,
    ) {
        $this->apiUrl = trim($apiUrl, '/');
    }

    public function body(
        array $body,
        RequestBodyFormat $format,
    ): StreamInterface {
        $streamFactory = $this->getHttpStreamFactory();

        switch ($format) {
            case RequestBodyFormat::JSON:
                return $streamFactory->createStream(json_encode($body, JSON_THROW_ON_ERROR));
            case RequestBodyFormat::POST:
                return $streamFactory->createStream(http_build_query($body, '', '&'));
        }

        throw new Exception('Invalid body format');
    }

    public function delete(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface {
        $headers['User-Agent'] ??= $this->getUserAgent();

        return new Request(
            factory: $this,
            options: $options,
            method: RequestMethod::DELETE,
            url: $url,
            body: $body,
            headers: $headers,
        );
    }

    public function get(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface {
        $headers['User-Agent'] = $this->getUserAgent();

        return new Request(
            factory: $this,
            options: $options,
            method: RequestMethod::GET,
            url: $url,
            body: $body,
            headers: $headers,
        );
    }

    public function getEndpointHeaders(array $headers = []): array
    {
        if (null !== $this->authorizationHeader) {
            $headers['Authorization'] = $this->authorizationHeader;
        }

        return $headers;
    }

    public function getEndpointUrl(string $endpoint): string
    {
        return $this->apiUrl . '/' . trim($endpoint, '/');
    }

    public function getHttpClient(): ClientInterface
    {
        if (null === $this->httpClient) {
            $httpClient = Discover::httpClient();

            if (null === $httpClient) {
                throw new Exception('An available PSR-18 HTTP Client factory could not be discovered.');
            }

            $this->httpClient = $httpClient;
        }

        return $this->httpClient;
    }

    public function getHttpRequestFactory(): PsrRequestFactoryInterface
    {
        if (null === $this->httpRequestFactory) {
            $httpRequestFactory = Discover::httpRequestFactory();

            if (null === $httpRequestFactory) {
                throw new Exception('An available PSR-17 HTTP Request factory could not be discovered.');
            }

            $this->httpRequestFactory = $httpRequestFactory;
        }

        return $this->httpRequestFactory;
    }

    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        if (null === $this->httpStreamFactory) {
            $httpStreamFactory = Discover::httpStreamFactory();

            if (null === $httpStreamFactory) {
                throw new Exception('An available PSR-17 Stream factory could not be discovered.');
            }

            $this->httpStreamFactory = $httpStreamFactory;
        }

        return $this->httpStreamFactory;
    }

    public function getLastRequest(): ?PsrRequestInterface
    {
        return $this->lastRequest;
    }

    public function getLastResponse(): ?ResponseInterface
    {
        return $this->lastResponse;
    }

    public function getUserAgent(): string
    {
        return sprintf('openfga-sdk php/%s', Client::VERSION);
    }

    public function post(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface {
        $headers['User-Agent'] ??= $this->getUserAgent();

        return new Request(
            factory: $this,
            options: $options,
            method: RequestMethod::POST,
            url: $url,
            body: $body,
            headers: $headers,
        );
    }

    public function put(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface {
        $headers['User-Agent'] ??= $this->getUserAgent();

        return new Request(
            factory: $this,
            options: $options,
            method: RequestMethod::PUT,
            url: $url,
            body: $body,
            headers: $headers,
        );
    }

    public function send(PsrRequestInterface $request): ResponseInterface
    {
        $httpClient = $this->getHttpClient();

        $this->lastRequest = $request;

        try {
            $this->lastResponse = $httpClient->sendRequest($request);
        } catch (Exception $e) {
            throw new Exception('API request issuance failed');
        }

        return $this->lastResponse;
    }
}
