<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use Exception;
use OpenFGA\Client;
use OpenFGA\Exceptions\{ConfigurationError, NetworkError};
use OpenFGA\Requests\RequestInterface as ClientRequestInterface;
use Override;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface};

use PsrDiscovery\Discover;

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

    #[Override]
    /**
     * @inheritDoc
     */
    public function getHttpClient(): ClientInterface
    {
        if (! $this->httpClient instanceof ClientInterface) {
            $httpClient = Discover::httpClient();

            if (null === $httpClient) {
                throw ConfigurationError::HttpClientMissing->exception();
            }

            $this->httpClient = $httpClient;
        }

        return $this->httpClient;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getHttpRequestFactory(): RequestFactoryInterface
    {
        if (! $this->httpRequestFactory instanceof RequestFactoryInterface) {
            $httpRequestFactory = Discover::httpRequestFactory();

            if (null === $httpRequestFactory) {
                throw ConfigurationError::HttpRequestFactoryMissing->exception();
            }

            $this->httpRequestFactory = $httpRequestFactory;
        }

        return $this->httpRequestFactory;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getHttpResponseFactory(): ResponseFactoryInterface
    {
        if (! $this->httpResponseFactory instanceof ResponseFactoryInterface) {
            $httpResponseFactory = Discover::httpResponseFactory();

            if (null === $httpResponseFactory) {
                throw ConfigurationError::HttpResponseFactoryMissing->exception();
            }

            $this->httpResponseFactory = $httpResponseFactory;
        }

        return $this->httpResponseFactory;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function getHttpStreamFactory(): StreamFactoryInterface
    {
        if (! $this->httpStreamFactory instanceof StreamFactoryInterface) {
            $httpStreamFactory = Discover::httpStreamFactory();

            if (null === $httpStreamFactory) {
                throw ConfigurationError::HttpStreamFactoryMissing->exception();
            }

            $this->httpStreamFactory = $httpStreamFactory;
        }

        return $this->httpStreamFactory;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function request(ClientRequestInterface $request): RequestInterface
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
            $request = $request->withHeader((string) $name, $value);
        }

        if ($body instanceof \Psr\Http\Message\StreamInterface) {
            return $request->withBody($body);
        }

        return $request;
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->getHttpClient()->sendRequest($request);
    }

    #[Override]
    /**
     * @inheritDoc
     */
    public static function handleResponseException(
        ResponseInterface $response,
        ?RequestInterface $request = null,
    ): never {
        $handles = [
            400 => NetworkError::Invalid,
            401 => NetworkError::Unauthenticated,
            403 => NetworkError::Forbidden,
            404 => NetworkError::UndefinedEndpoint,
            409 => NetworkError::Conflict,
            422 => NetworkError::Timeout,
            500 => NetworkError::Server,
        ];

        if (isset($handles[$response->getStatusCode()])) {
            $error = self::parseError($response);

            throw $handles[$response->getStatusCode()]->exception(request: $request, response: $response, context: ['%error%' => $error]);
        }

        throw NetworkError::Unexpected->exception(request: $request, response: $response, context: ['%error%' => 'API responded with an unexpected status code: ' . $response->getStatusCode()]);
    }

    private static function parseError(ResponseInterface $response): string
    {
        $error = '';

        try {
            $error = trim((string) $response->getBody());
            $decoded = json_decode($error, true, 512, JSON_THROW_ON_ERROR);
            return $decoded ?? $error;
        } catch (Exception) {
            if ('' !== $error) {
                return $error;
            }
        }

        return 'Unknown error';
    }
}
