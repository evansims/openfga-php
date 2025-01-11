<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{CreateStoreRequest, CreateStoreResponse, ListStoresResponse};
use OpenFGA\API\Options\{CreateStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\API\Request;
use OpenFGA\ClientInterface;
use OpenFGA\SDK\Exceptions\Endpoints\OktaUnsupportedEndpoint;

final class StoresEndpoint
{
    public function __construct(
        private ClientInterface $client,
    ) {
    }

    public function create(string $name, ?CreateStoreRequestOptions $options = null): CreateStoreResponse
    {
        if (null === $options) {
            $options = new CreateStoreRequestOptions();
        }

        $body = new CreateStoreRequest([
            'name' => $name,
        ]);

        $api = new Request(
        client: $this->client,
            options: $options,
            endpoint: '/stores',
            body: (array)$body
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $api->getResponseBodyJson();

        return new CreateStoreResponse($json);
    }

    public function list(?ListStoresRequestOptions $options = null): ListStoresResponse
    {
        if ($this->client->getConfiguration()->getUseOkta()) {
            throw new OktaUnsupportedEndpoint();
        }

        if (null === $options) {
            $options = new ListStoresRequestOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores',
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('GET /stores failed');
        }

        $json = $api->getResponseBodyJson();

        return new ListStoresResponse($json);
    }
}
