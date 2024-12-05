<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Utilities;

use Exception;
use OpenFGA\Client;
use OpenFGA\ClientInterface as OpenFGAClientInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use PsrDiscovery\Discover;

use function sprintf;

enum RequestBodyFormat: int
{
    case JSON = 1;
    case POST = 2;
    case MULTIPART = 3;
}

final class Network
{
    public function __construct(
        private OpenFGAClientInterface $client,
        private ?ClientInterface $httpClient = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
    ) {
    }

    public function getUserAgent(): string
    {
        return sprintf('openfga-sdk php/%s', Client::VERSION);
    }

    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        if ($this->httpRequestFactory === null) {
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
        if ($this->httpStreamFactory === null) {
            $httpStreamFactory = Discover::httpStreamFactory();

            if (null === $httpStreamFactory) {
                throw new Exception('An available PSR-17 Stream factory could not be discovered.');
            }

            $this->httpStreamFactory = $httpStreamFactory;
        }

        return $this->httpStreamFactory;
    }

    public function getHttpClient(): ClientInterface
    {
        if ($this->httpClient === null) {
            $httpClient = Discover::httpClient();

            if (null === $httpClient) {
                throw new Exception('An available PSR-18 HTTP Client factory could not be discovered.');
            }

            $this->httpClient = $httpClient;
        }

        return $this->httpClient;
    }

    public function createRequestBody(array $body, RequestBodyFormat $format): StreamInterface
    {
        $factory = $this->getHttpStreamFactory();

        switch ($format) {
            case RequestBodyFormat::JSON:
                return $factory->createStream(json_encode($body, JSON_THROW_ON_ERROR));
            case RequestBodyFormat::POST:
                return $factory->createStream(http_build_query($body, '', '&'));
        }
    }

    public function createRequest(
        string $method,
        string $url,
        array $headers = [],
        ?StreamInterface $body = null
    ): RequestInterface {
        $requestFactory = $this->getHttpRequestFactory();

        try {
            $request = $requestFactory->createRequest(method: $method, uri: $url);

            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }

            if ($body !== null) {
                $request = $request->withBody($body);
            }

        } catch (Exception $e) {
            throw new Exception('HTTP request creation failed');
        }

        return $request;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $httpClient = $this->getHttpClient();

        try {
            $response = $httpClient->sendRequest($request);
        } catch (Exception $e) {
            throw new Exception('API request issuance failed');
        }

        return $response;
    }
}
