<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Endpoints\Traits\{AuthorizationModelIdentifiers, StoreIdentifiers};
use OpenFGA\API\Models\GetStoreResponse;
use OpenFGA\API\Options\{DeleteStoreRequestOptions, GetStoreRequestOptions};
use OpenFGA\API\Request;
use OpenFGA\ClientInterface;

final class StoreEndpoint
{
    use StoreIdentifiers, AuthorizationModelIdentifiers;

    public function __construct(
        private ClientInterface $client,
        private ?string $storeId = null,
    ) {
    }

    public function delete(?string $storeId, ?DeleteStoreRequestOptions $options = null): void
    {
        $storeId ??= $this->getStoreId();

        if (null === $options) {
            $options = new DeleteStoreRequestOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId,
        );

        $response = $api->delete();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("DELETE /store/{$storeId} failed");
        }

        return;
    }

    public function get(?string $storeId = null, ?GetStoreRequestOptions $options = null): GetStoreResponse
    {
        $storeId ??= $this->getStoreId();

        if (null === $options) {
            $options = new GetStoreRequestOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId,
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("GET /store/{$storeId} failed");
        }

        $json = $api->getResponseBodyJson();

        return new GetStoreResponse($json);
    }

    public function models(?string $storeId = null): ModelsEndpoint
    {
        return new ModelsEndpoint($this->client, $storeId ?? $this->getStoreId());
    }

    public function model(?string $modelId = null, ?string $storeId = null): ModelEndpoint
    {
        return new ModelEndpoint($this->client, $modelId, $storeId ?? $this->getStoreId());
    }
}
