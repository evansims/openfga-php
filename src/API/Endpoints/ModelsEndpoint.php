<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Endpoints\Traits\StoreIdentifiers;
use OpenFGA\API\Models\{ReadAuthorizationModelsResponse, WriteAuthorizationModelRequest, WriteAuthorizationModelResponse};
use OpenFGA\API\Options\{GetAuthorizationModelsOptions, CreateAuthorizationModelOptions};
use OpenFGA\API\Request;
use OpenFGA\ClientInterface;

final class ModelsEndpoint
{
    use StoreIdentifiers;

    public function __construct(
        private ClientInterface $client,
        private ?string $storeId = null,
    ) {}

    public function list(?string $storeId = null, ?GetAuthorizationModelsOptions $options = null): ReadAuthorizationModelsResponse
    {
        $storeId ??= $this->getStoreId();

        if (null === $options) {
            $options = new GetAuthorizationModelsOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models',
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $api->getResponseBodyJson();

        return new ReadAuthorizationModelsResponse($json);
    }

    public function create(?array $typeDefinitions = null, ?string $schemaVersion = null, ?array $conditions, ?string $storeId = null, ?CreateAuthorizationModelOptions $options = null): WriteAuthorizationModelResponse
    {
        $storeId ??= $this->getStoreId();

        if (null === $options) {
            $options = new CreateAuthorizationModelOptions();
        }

        $body = [];

        if ($typeDefinitions !== null) {
            $body['typeDefinitions'] = $typeDefinitions;
        }

        if ($schemaVersion !== null) {
            $body['schemaVersion'] = $schemaVersion;
        }

        if ($conditions !== null) {
            $body['conditions'] = $conditions;
        }

        $body = new WriteAuthorizationModelRequest($body);

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId . '/authorization-models',
            body: (array)$body
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to create authorization model');
        }

        $json = $api->getResponseBodyJson();

        return new WriteAuthorizationModelResponse($json);
    }
}
