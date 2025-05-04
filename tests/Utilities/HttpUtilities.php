<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Utilities;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use PsrDiscovery\Discover;
use PsrMock\Psr18\Contracts\ClientContract;

final class HttpUtilities
{
    public static function getHttpClient(): ClientContract
    {
        if (Discover::httpClient() === null) {
            throw new \Exception('HTTP client not found');
        }

        return Discover::httpClient();
    }

    public static function getHttpResponseFactory(): ResponseFactoryInterface
    {
        if (Discover::httpResponseFactory() === null) {
            throw new \Exception('HTTP response factory not found');
        }

        return Discover::httpResponseFactory();
    }

    public static function createHttpResponse(
        ResponseFactoryInterface $responseFactory,
        int $statusCode = 200,
        array | null $body = null,
        array $headers = ['Content-Type' => 'application/json'],
    ): ResponseInterface {
        $response = $responseFactory->createResponse($statusCode);

        foreach ($headers as $header => $value) {
            $response = $response->withHeader($header, $value);
        }

        if ($body !== null) {
            $response->getBody()->write(json_encode($body, JSON_THROW_ON_ERROR));
        }

        return $response;
    }
}
