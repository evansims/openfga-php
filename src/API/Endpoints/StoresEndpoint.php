<?php

declare(strict_types=1);

namespace OpenFGA\API\Endpoints;

use OpenFGA\API\Models\{CreateStoreRequest, CreateStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\API\{Request, RequestEndpoint, RequestMethod};
use OpenFGA\API\Options\{CreateStoreRequestOptions, DeleteStoreRequestOptions, GetStoreRequestOptions, ListStoresRequestOptions};

abstract class StoresEndpoint extends Endpoint
{
    public function listStores(?ListStoresRequestOptions $options = null): ListStoresResponse
    {
        if ($options === null) {
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

    public function createStore(CreateStoreRequest $request, ?CreateStoreRequestOptions $options = null): CreateStoreResponse
    {
        if ($options === null) {
            $options = new CreateStoreRequestOptions();
        }

        return new CreateStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    public function getStore(?string $storeId, ?GetStoreRequestOptions $options = null): GetStoreResponse
    {
        if ($options === null) {
            $options = new GetStoreRequestOptions();
        }

        return new GetStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    public function deleteStore(?string $storeId, ?DeleteStoreRequestOptions $options = null): void
    {
        if ($options === null) {
            $options = new DeleteStoreRequestOptions();
        }

        return;
    }
}
