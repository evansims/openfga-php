<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Utilities;

use OpenFGA\ClientInterface;
use OpenFGA\Client;
use OpenFGA\ConfigurationInterface;
use OpenFGA\Configuration;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use PsrMock\Psr18\Contracts\ClientContract;

final class ClientUtilities
{
    public static function getClient(
        ?ConfigurationInterface $configuration = null,
        ?ClientContract $httpClient = null
    ): ClientInterface
    {
        if ($configuration !== null) {
            return new Client($configuration);
        }

        return new Client(self::getConfiguration($httpClient));
    }

    public static function getConfiguration(?ClientContract $httpClient = null): ConfigurationInterface
    {
        return new Configuration(
            apiUrl: 'http://test.fga.api',
            storeId: 'test_store_id',
            authorizationModelId: 'test_auth_model_id',
            httpClient: $httpClient ?? HttpUtilities::getHttpClient()
        );
    }

    public static function getHttpRequest(ClientInterface $client): ?RequestInterface
    {
        return $client->lastRequest;
    }

    public static function getHttpResponse(ClientInterface $client): ?ResponseInterface
    {
        return $client->lastResponse;
    }
}
