<?php

declare(strict_types=1);

use OpenFGA\Client;

it('creates and deletes a store', function (): void {
    $url = getenv('FGA_API_URL') ?: 'http://localhost:8080';
    $client = new Client(url: $url);
    $createdStoreId = null;

    $name = 'php-sdk-test-' . bin2hex(random_bytes(5));

    try {
        $response = $client->createStore(name: $name);
        $createdStoreId = $response->getId();
        expect($createdStoreId)->not()->toBe('');
        expect($response->getName())->toBe($name);

        $delete = $client->deleteStore(store: $createdStoreId);
        expect($delete)->toBeInstanceOf(OpenFGA\Responses\DeleteStoreResponseInterface::class);
    } finally {
        // Clean up the store if it was created but not deleted
        if (null !== $createdStoreId && null !== $client->getStore($createdStoreId)) {
            $client->deleteStore(store: $createdStoreId);
        }
    }
});

it('retrieves a created store', function (): void {
    $url = getenv('FGA_API_URL') ?: 'http://localhost:8080';
    $client = new Client(url: $url);
    $createdStoreId = null;

    $name = 'php-sdk-test-' . bin2hex(random_bytes(5));

    try {
        $create = $client->createStore(name: $name);
        $createdStoreId = $create->getId();

        $get = $client->getStore(store: $createdStoreId);
        expect($get->getStore()->getName())->toBe($name);

        $list = $client->listStores();
        $ids = [];
        foreach ($list->getStores() as $store) {
            $ids[] = $store->getId();
        }
        expect($ids)->toContain($createdStoreId);
    } finally {
        if (null !== $createdStoreId && null !== $client->getStore($createdStoreId)) {
            $client->deleteStore(store: $createdStoreId);
        }
    }
});
