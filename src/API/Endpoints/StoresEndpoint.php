<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{CreateStoreRequest, CreateStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\API\Options\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};
use OpenFGA\API\{Request, RequestEndpoint, RequestMethod};

abstract class StoresEndpoint extends Endpoint
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

    final public function getStore(?string $storeId, ?GetStoreRequestOptions $options = null): GetStoreResponse
    {
        if (null === $options) {
            $options = new GetStoreRequestOptions();
        }

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

        $call = new Request(
            configuration: $this->configuration,
            options: $options,
            method: RequestMethod::GET,
            endpoint: RequestEndpoint::LIST_STORES,
        );

        return new ListStoresResponse([
            [
                'id' => uniqid(),
                'name' => 'Example Store',
            ],
        ]);
    }
}
