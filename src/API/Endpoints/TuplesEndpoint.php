<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Endpoints\Traits\{StoreIdentifiers, AuthorizationModelIdentifiers};
use OpenFGA\API\Models\{ReadChangesResponse, WriteRequest};
use OpenFGA\API\Options\{ReadChangesOptions, WriteOptions};
use OpenFGA\API\Request;
use OpenFGA\API\TuplesCollection;
use OpenFGA\ClientInterface;

final class TuplesEndpoint
{
    use StoreIdentifiers, AuthorizationModelIdentifiers;

    public function __construct(
        private ClientInterface $client,
        private ?string $modelId = null,
        private ?string $storeId = null,
    ) {
    }

    final public function changes(?string $storeId = null,?ReadChangesOptions $options = null): ReadChangesResponse
    {
        $storeId ??= $this->getStoreId();

        if (null === $options) {
            $options = new ReadChangesOptions();
        }

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId . '/changes',
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("GET /stores/{$storeId}/changes failed");
        }

        $json = $api->getResponseBodyJson();

        return new ReadChangesResponse($json);
    }

    final public function write(?TuplesCollection $writes = null, ?TuplesCollection $deletes = null, ?string $storeId = null, ?WriteOptions $options = null): void
    {
        $storeId ??= $this->getStoreId();
        $modelId ??= $this->getModelId();

        if (null === $options) {
            $options = new WriteOptions();
        }

        $body = [];

        if ($writes !== null && count($writes) > 0) {
            $body['writes'] = ['tuple_keys' => (array)$writes];
        }

        if (!$deletes !== null && count($deletes) > 0) {
            $body['deletes'] = ['tuple_keys' => (array)$deletes];
        }

        if ($body === []) {
            return;
        }

        $body['authorization_model_id'] = $modelId;

        $body = new WriteRequest($body);

        $api = new Request(
            client: $this->client,
            options: $options,
            endpoint: '/stores/' . $storeId . '/write',
            body: (array)$body,
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("POST /stores/{$storeId}/write failed");
        }

        return;
    }
}
