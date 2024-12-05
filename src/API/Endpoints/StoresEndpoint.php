<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{CreateStoreRequest, CreateStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\API\Options\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\API\Request;

trait StoresEndpoint
{
    final public function createStore(CreateStoreRequest $request, ?CreateStoreRequestOptions $options = null): CreateStoreResponse
    {
        if (null === $options) {
            $options = new CreateStoreRequestOptions();
        }

        return new CreateStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    final public function deleteStore(?string $storeId, ?DeleteStoreRequestOptions $options = null): void
    {
        if (null === $options) {
            $options = new DeleteStoreRequestOptions();
        }

    }

    final public function getStore(?string $storeId = null, ?GetStoreRequestOptions $options = null): GetStoreResponse
    {
        $storeId ??= $this->getConfiguration()->getStoreId();

        if (null === $options) {
            $options = new GetStoreRequestOptions();
        }

        $response = (new Request(
            client: $this,
            options: $options,
            endpoint: '/stores/' . $storeId,
        ))->get();

        return new GetStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    final public function listStores(?ListStoresRequestOptions $options = null): ListStoresResponse
    {
        if (null === $options) {
            $options = new ListStoresRequestOptions();
        }

        $response = (new Request(
            client: $this,
            options: $options,
            endpoint: '/stores',
        ))->get();

        return new ListStoresResponse([
            [
                'id' => uniqid(),
                'name' => 'Example Store',
            ],
        ]);
    }
}
