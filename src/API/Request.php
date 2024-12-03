<?php

declare(strict_types=1);

namespace OpenFGA\API;

use Exception;
use OpenFGA\Client;
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;

use PsrDiscovery\Discover;

use function sprintf;

enum RequestMethod: string
{
    case DELETE = 'DELETE';

    case GET = 'GET';

    case POST = 'POST';

    case PUT = 'PUT';
}

enum RequestEndpoint: string
{
    case CREATE_STORE = '/stores';

    case DELETE_STORE = '/stores/%storeId';

    case GET_STORE = '/stores/%storeId';

    case LIST_STORES = '/stores';
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
        $requestFactory = Discover::httpRequestFactory();
        $httpClient = Discover::httpClient();

        if (null === $requestFactory || null === $httpClient) {
            throw new Exception('HTTP client not found');
        }

        try {
            $request = $requestFactory->createRequest(
                $method->value,
                $configuration->apiUrl . $endpoint,
                $headers,
                json_encode($body),
            );
        } catch (Exception $e) {
            throw new Exception('HTTP request creation failed');
        }

        try {
            $response = $httpClient->sendRequest($request);
        } catch (Exception $e) {
            throw new Exception('API request issuance failed');
        }

        if (200 !== $response->getStatusCode()) {
            throw new Exception('API request failed');
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}
