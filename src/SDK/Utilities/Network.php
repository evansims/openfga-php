<?php

declare(strict_types=1);

namespace OpenFGA\SDK\Utilities;

use Exception;
use OpenFGA\Client;
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
    // TODO: Accept a container/DI object as a parameter to allow for singletons of PSR-17 and PSR-18 factories

    public function getUserAgent(): string
    {
        return sprintf('openfga-sdk php/%s', Client::VERSION);
    }

    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        $httpRequestFactory = Discover::httpRequestFactory();

        if (null === $httpRequestFactory) {
            throw new Exception('An available PSR-17 HTTP Request factory could not be discovered.');
        }

        return $httpRequestFactory;
    }

    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        $httpStreamFactory = Discover::httpStreamFactory();

        if (null === $httpStreamFactory) {
            throw new Exception('An available PSR-17 Stream factory could not be discovered.');
        }

        return $httpStreamFactory;
    }

    public function getHttpClient(): ClientInterface
    {
        $httpClient = Discover::httpClient();

        if (null === $httpClient) {
            throw new Exception('An available PSR-18 HTTP Client factory could not be discovered.');
        }

        return $httpClient;
    }

    public function createRequestBody(array $body, RequestBodyFormat $format): StreamInterface
    {
        $factory = $this->getHttpStreamFactory();

        switch ($format) {
            case RequestBodyFormat::JSON:
                return $factory->createStream(json_encode($body, JSON_THROW_ON_ERROR));
            case RequestBodyFormat::POST:
                return $factory->createStream(http_build_query($body));
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
