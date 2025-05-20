<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Exception;
use OpenFGA\Client;
use OpenFGA\Exceptions\{ApiEndpointException, ApiForbiddenException, ApiInternalServerException, ApiTimeoutException, ApiTransactionException, ApiUnauthenticatedException, ApiValidationException};
use OpenFGA\Requests\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface as MessageRequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface};
use PsrDiscovery\Discover;

use function is_string;
use function sprintf;

enum RequestMethod: string
{
    case DELETE = 'DELETE';

    case GET = 'GET';

    case POST = 'POST';

    case PUT = 'PUT';
}

final class RequestManager implements RequestManagerInterface
{
    public function __construct(
        private string $url,
        private int $maxRetries,
        private ?string $authorizationHeader = null,
        private ?ClientInterface $httpClient = null,
        private ?ResponseFactoryInterface $httpResponseFactory = null,
        private ?StreamFactoryInterface $httpStreamFactory = null,
        private ?RequestFactoryInterface $httpRequestFactory = null,
    ) {
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

    public function getHttpRequestFactory(): RequestFactoryInterface
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

    public function getHttpResponseFactory(): ResponseFactoryInterface
    {
        if (null === $this->httpResponseFactory) {
            $httpResponseFactory = Discover::httpResponseFactory();

            if (null === $httpResponseFactory) {
                throw new Exception('An available PSR-17 HTTP Response factory could not be discovered.');
            }

            $this->httpResponseFactory = $httpResponseFactory;
        }

        return $this->httpResponseFactory;
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

    public function request(RequestInterface $request): MessageRequestInterface
    {
        $request = $request->getRequest($this->getHttpStreamFactory());

        $method = $request->getMethod();
        $uri = $request->getUrl();
        $headers = $request->getHeaders();
        $body = $request->getBody();

        if ($request->useApiUrl()) {
            $uri = $this->url . '/' . trim($uri, '/');
        }

        $headers['User-Agent'] = sprintf('openfga-sdk php/%s', Client::VERSION);

        if (null !== $this->authorizationHeader) {
            $headers['Authorization'] = $this->authorizationHeader;
        }

        $request = $this->getHttpRequestFactory()->createRequest(
            method: $method->value,
            uri: $uri,
        );

        foreach ($headers as $name => $value) {
            if (! is_string($value)) {
                throw new Exception('Header value must be a string.');
            }

            $request = $request->withHeader((string) $name, $value);
        }

        if (null !== $body) {
            $request = $request->withBody($body);
        }

        return $request;
    }

    public function send(MessageRequestInterface $request): ResponseInterface
    {
        return $this->getHttpClient()->sendRequest($request);
    }

    public static function handleResponseException(ResponseInterface $response): void
    {
        $error = '';

        try {
            $error = trim((string) $response->getBody());
        } catch (Exception) {
        }

        if ('' === $error) {
            $error = 'Unknown error';
        }

        $error = json_encode($error, JSON_THROW_ON_ERROR);

        if (400 === $response->getStatusCode()) {
            throw new ApiValidationException($error);
        }

        if (401 === $response->getStatusCode()) {
            throw new ApiUnauthenticatedException($error);
        }

        if (403 === $response->getStatusCode()) {
            throw new ApiForbiddenException($error);
        }

        if (404 === $response->getStatusCode()) {
            throw new ApiEndpointException($error);
        }

        if (409 === $response->getStatusCode()) {
            throw new ApiTransactionException($error);
        }

        if (422 === $response->getStatusCode()) {
            throw new ApiTimeoutException($error);
        }

        if (500 === $response->getStatusCode()) {
            throw new ApiInternalServerException($error);
        }
    }
}
