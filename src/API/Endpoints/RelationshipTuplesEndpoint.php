<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{ReadChangesResponse, ReadRequest, ReadResponse, WriteRequest};
use OpenFGA\API\Options\{ReadChangesOptions, ReadOptions, WriteOptions};
use OpenFGA\API\Request;

trait RelationshipTuplesEndpoint
{
    final public function read(ReadRequest $request, ?string $storeId = null,?ReadOptions $options = null): ReadResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new ReadOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/read',
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("POST /stores/{$storeId}/read failed");
        }

        $json = $api->getResponseBodyJson();

        return new ReadResponse($json);
    }

    final public function readChanges(?string $storeId = null,?ReadChangesOptions $options = null): ReadChangesResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new ReadChangesOptions();
        }

        $api = new Request(
            client: $this,
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

    final public function write(WriteRequest $request, ?string $storeId = null,?WriteOptions $options = null): void
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new WriteOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId . '/write',
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("POST /stores/{$storeId}/write failed");
        }

        return;
    }
}
