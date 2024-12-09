<?php

declare(strict_types=1);

namespace OpenFGA\API;

use Exception;
use OpenFGA\ClientInterface;
use OpenFGA\SDK\Utilities\Network;
use OpenFGA\SDK\Utilities\RequestBodyFormat;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

final class Request
{
    public function __construct(
        private ClientInterface $client,
        private RequestOptions $options,
        private string $endpoint,
        private array $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
        private array $body = [],
        private ?Network $network = null,
        private ?ResponseInterface $response = null,
    ) {
        $this->network ??= new Network(
            client: $client
        );
    }

    public function getRequestOptions(): RequestOptions
    {
        return $this->options;
    }

    public function getRequestEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getRequestHeaders(): array
    {
        return $this->headers;
    }

    public function getRequestBody(): array
    {
        return $this->body;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getResponseBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function getResponseBodyJson(): array
    {
        return json_decode($this->getResponseBody()->getContents(), true);
    }

    public function get(): ResponseInterface
    {
        $request = $this->buildRequest('GET');

        // TODO: Support auto-retry w/ jitter for throttled requests

        $response = $this->network->getHttpClient()->sendRequest($request);

        return $this->response = $response;
    }

    public function post(): ResponseInterface
    {
        $request = $this->buildRequest('POST');
        $response = $this->network->getHttpClient()->sendRequest($request);

        // var_dump($response);
        // var_dump($response->getBody()->getContents());
        // var_dump($response->getHeaders());
        // exit;

        // TODO: Support auto-retry w/ jitter for throttled requests

        $response = $this->network->getHttpClient()->sendRequest($request);

        return $this->response = $response;
    }

    public function delete(): ResponseInterface
    {
        $request = $this->buildRequest('DELETE');
        $response = $this->network->getHttpClient()->sendRequest($request);

        // TODO: Support auto-retry w/ jitter for throttled requests

        $response = $this->network->getHttpClient()->sendRequest($request);

        return $this->response = $response;
    }

    private function buildRequest(
        string $method
    ): RequestInterface {
        $headers = $this->getRequestHeaders();
        $body = null;

        if (! isset($headers['Authorization'])) {
            $auth = $this->client->getCredentialManager()->getAuthorizationHeader();

            if ($auth) {
                $headers['Authorization'] = $auth;
            }
        }

        if ($this->getRequestBody()) {
            $body = $this->network->createRequestBody($this->getRequestBody(), RequestBodyFormat::JSON);
        }

        return $this->network->createRequest(
            $method,
            $this->client->getConfiguration()->getApiUrl() . $this->getRequestEndpoint(),
            $headers,
            $body,
        );
    }
}
