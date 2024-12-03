<?php

declare(strict_types=1);

namespace OpenFGA;

use OpenFGA\API\Model\{CreateStoreRequest, CreateStoreResponse, GetStoreResponse, ListStoresResponse};
use OpenFGA\SDK\Configuration\ClientConfigurationInterface;
use OpenFGA\SDK\Endpoints\{CreateStoreOptions, DeleteStoreOptions, GetStoreOptions, ListStoresOptions};

final class Client
{
    public const string VERSION = '0.1.0';

    public function __construct(
        public ClientConfigurationInterface $configuration,
    ) {
    }

    public function listStores(?ListStoresOptions $options = null): ListStoresResponse
    {
        if ($options === null) {
            $options = new ListStoresOptions();
        }

        return new ListStoresResponse([
            [
                'id' => uniqid(),
                'name' => 'Example Store',
            ],
        ]);
    }

    public function createStore(CreateStoreRequest $request, ?CreateStoreOptions $options = null): CreateStoreResponse
    {
        if ($options === null) {
            $options = new CreateStoreOptions();
        }

        return new CreateStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    public function getStore(?string $storeId, ?GetStoreOptions $options = null): GetStoreResponse
    {
        if ($options === null) {
            $options = new GetStoreOptions();
        }

        return new GetStoreResponse([
            'id' => uniqid(),
            'name' => 'Example Store',
        ]);
    }

    public function deleteStore(?string $storeId, ?DeleteStoreOptions $options = null): void
    {
        if ($options === null) {
            $options = new DeleteStoreOptions();
        }

        return;
    }
}
