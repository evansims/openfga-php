<?php

declare(strict_types=1);

namespace OpenFGA\API;

use OpenFGA\Client;
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

enum RequestMethod: string
{
    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
}

enum RequestEndpoint: string
{
    case LIST_STORES = '/stores';
    case CREATE_STORE = '/stores';
    case GET_STORE = '/stores/%storeId';
    case DELETE_STORE = '/stores/%storeId';
}

final class Request
{
    public string $user_agent = sprintf('openfga-sdk php/%s', Client::VERSION);

    public function __construct(
        public ClientConfigurationInterface $configuration,
        public RequestOptions $options,
        public RequestMethod $method,
        public RequestEndpoint $endpoint,
        public array $headers = ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
        public array $body = [],
    ) {
    }
}
