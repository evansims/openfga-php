<?php

declare(strict_types=1);

namespace OpenFGA\Requests;

use OpenFGA\RequestOptions\RequestOptionsInterface;
use Psr\Http\Message\{RequestInterface as PsrRequestInterface, ResponseInterface, StreamInterface};

interface RequestInterface
{
    public function build(): PsrRequestInterface;

    public function get(): ResponseInterface;

    public function getRequest(): PsrRequestInterface;

    public function getRequestBody(): ?StreamInterface;

    public function getRequestHeaders(): array;

    public function getRequestOptions(): RequestOptionsInterface;

    public function getResponse(): ResponseInterface;

    public function getResponseBody(): StreamInterface;

    public function getResponseBodyJson(): array;

    public function getUrl(): string;

    public function post(): ResponseInterface;

    public function put(): ResponseInterface;

    public function send(): ResponseInterface;
}
