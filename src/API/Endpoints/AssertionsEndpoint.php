<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{ReadAssertionsResponse, WriteAssertionsRequest};
use OpenFGA\API\Options\{ReadAssertionsOptions};
use OpenFGA\API\Request;

trait AssertionsEndpoint
{
    final public function readAssertions(?string $authorizationModelId = null, ?string $storeId = null, ?ReadAssertionsOptions $options = null): ReadAssertionsResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();
        $id ??= $this->getConfiguration()->getAuthorizationModelId();

        if (null === $options) {
            $options = new ReadAssertionsOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/assertions/' . $authorizationModelId,
        );

        $response = $api->get();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('GET /stores/' . $storeId . '/assertions/' . $authorizationModelId . ' failed');
        }

        $json = $api->getResponseBodyJson();

        return new ReadAssertionsResponse($json);
    }

    final public function writeAssertions(WriteAssertionsRequest $request, ?string $authorizationModelId = null, ?string $storeId = null, ?ReadAssertionsOptions $options = null): void
    {
        $storeId ??= $this->getConfiguration()->getStoreId();
        $id ??= $this->getConfiguration()->getAuthorizationModelId();

        if (null === $options) {
            $options = new ReadAssertionsOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/assertions/' . $authorizationModelId,
        );

        $response = $api->put();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('PUT /stores/' . $storeId . '/assertions/' . $authorizationModelId . ' failed');
        }

        return;
    }
}
