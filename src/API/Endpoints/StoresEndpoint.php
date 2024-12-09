<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{CreateStoreRequest, CreateStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\API\Options\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\API\Request;
use OpenFGA\SDK\Exceptions\Endpoints\OktaUnsupportedEndpoint;

trait StoresEndpoint
{
    final public function createStore(CreateStoreRequest $request, ?CreateStoreRequestOptions $options = null): CreateStoreResponse
    {
        if (null === $options) {
            $options = new CreateStoreRequestOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores',
        );

        $response = $api->post();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Failed to get authorization models');
        }

        $json = $api->getResponseBodyJson();

        return new CreateStoreResponse($json);
    }

    final public function deleteStore(?string $storeId, ?DeleteStoreRequestOptions $options = null): void
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new DeleteStoreRequestOptions();
        }

        $api = new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId,
        );

        $response = $api->delete();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception("DELETE /store/{$storeId} failed");
        }

        return;
    }

    final public function getStore(?string $storeId = null, ?GetStoreRequestOptions $options = null): GetStoreResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new GetStoreRequestOptions();
        }

        $api = new Request(
            client: $this,
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

    final public function listStores(?ListStoresRequestOptions $options = null): ListStoresResponse
    {
        if ($this->getConfiguration()->useOkta()) {
            throw new OktaUnsupportedEndpoint();
        }

        if (null === $options) {
            $options = new ListStoresRequestOptions();
        }

        $api = new Request(
            client: $this,
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
