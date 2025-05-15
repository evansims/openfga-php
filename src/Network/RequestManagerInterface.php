<?php

declare(strict_types=1);

namespace OpenFGA\Network;

use OpenFGA\Requests\RequestInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\{RequestFactoryInterface, RequestInterface as MessageRequestInterface, ResponseFactoryInterface, ResponseInterface, StreamFactoryInterface};

interface RequestManagerInterface
{
    public function getHttpClient(): ClientInterface;

    public function getHttpRequestFactory(): RequestFactoryInterface;

    public function getHttpResponseFactory(): ResponseFactoryInterface;

    public function getHttpStreamFactory(): StreamFactoryInterface;

    public function request(RequestInterface $request): MessageRequestInterface;

    public function send(MessageRequestInterface $request): ResponseInterface;
}
