<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\RequestOptions\RequestOptionsInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface as PsrRequestFactoryInterface, RequestInterface as PsrRequestInterface, ResponseInterface, StreamFactoryInterface, StreamInterface};

enum RequestBodyFormat: int
{
    case JSON = 1;

    case MULTIPART = 3;

    case POST = 2;
}

interface RequestFactoryInterface
{
    public function body(
        array $body,
        RequestBodyFormat $format,
    ): StreamInterface;

    public function delete(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface;

    public function get(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface;

    public function getEndpointHeaders(): array;

    public function getEndpointUrl(string $path): string;

    public function getHttpClient(): ClientInterface;

    public function getHttpRequestFactory(): PsrRequestFactoryInterface;

    public function getHttpStreamFactory(): StreamFactoryInterface;

    public function getUserAgent(): string;

    public function post(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface;

    public function put(
        string $url,
        ?StreamInterface $body = null,
        array $headers = [],
        ?RequestOptionsInterface $options = null,
    ): RequestInterface;

    public function send(
        PsrRequestInterface $request,
    ): ResponseInterface;
}
