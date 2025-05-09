<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\RequestOptions\RequestOptionsInterface;
use Psr\Http\Message\{RequestInterface as PsrRequestInterface, ResponseInterface, StreamInterface};

enum RequestMethod: string
{
    case DELETE = 'DELETE';

    case GET = 'GET';

    case POST = 'POST';

    case PUT = 'PUT';
}

final class Request implements RequestInterface
{
    private ?PsrRequestInterface $request = null;

    private ?ResponseInterface $response = null;

    public function __construct(
        private RequestFactory $factory,
        private RequestOptionsInterface $options,
        private RequestMethod $method,
        private string $url,
        private ?StreamInterface $body = null,
        private array $headers = [],
    ) {
    }

    public function build(): PsrRequestInterface
    {
        $requestFactory = $this->factory->getHttpRequestFactory();
        $body = $this->getRequestBody();
        $headers = $this->headers;
        $url = $this->url;

        $queryParameters = $this->options->getQueryParameters();

        if (! empty($queryParameters)) {
            $url = $url . '?' . http_build_query($queryParameters);
        }

        $request = $requestFactory->createRequest(
            method: $this->method->value,
            uri: $url,
        );

        foreach ($headers as $name => $value) {
            $request = $request->withHeader($name, $value);
        }

        if ($body) {
            $request = $request->withBody($body);
        }

        $this->request = $request;

        return $request;
    }

    public function delete(): ResponseInterface
    {
        $this->send();

        return $this->response;
    }

    public function get(): ResponseInterface
    {
        $this->send();

        // TODO: Support auto-retry w/ jitter for throttled requests

        return $this->response;
    }

    public function getRequest(): PsrRequestInterface
    {
        if (null === $this->request) {
            $this->request = $this->build();
        }

        return $this->request;
    }

    public function getRequestBody(): ?StreamInterface
    {
        return $this->body;
    }

    public function getRequestHeaders(): array
    {
        return $this->headers;
    }

    public function getRequestOptions(): RequestOptionsInterface
    {
        return $this->options;
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
        return json_decode($this->getResponseBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function post(): ResponseInterface
    {
        $this->send();

        // TODO: Support auto-retry w/ jitter for throttled requests

        return $this->response;
    }

    public function put(): ResponseInterface
    {
        $this->send();

        // TODO: Support auto-retry w/ jitter for throttled requests

        return $this->response;
    }

    public function send(): ResponseInterface
    {
        $this->request = $this->build();

        $response = $this->factory->send($this->request);

        $this->response = $response;

        return $response;
    }
}
