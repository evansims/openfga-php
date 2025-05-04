<?php

declare(strict_types=1);

use OpenFGA\Models\Store;
use OpenFGA\RequestOptions\ListStoresRequestOptions;
use OpenFGA\Responses\{CreateStoreResponse, GetStoreResponse, ListStoresResponse};

it('lists stores successfully', function () {
    $mockResponse = [
        'stores' => [
            ['id' => 'store1', 'name' => 'Store One', 'created_at' => '2024-01-01T10:00:00Z', 'updated_at' => '2024-01-01T10:00:00Z'],
            ['id' => 'store2', 'name' => 'Store Two', 'created_at' => '2024-01-02T11:00:00Z', 'updated_at' => '2024-01-02T11:00:00Z'],
        ],
        'continuation_token' => bin2hex(random_bytes(16)),
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->listStores();

    expect($response)->toBeInstanceOf(ListStoresResponse::class)
        ->and($response->stores)->toHaveCount(2)
        ->and($response->stores[0])->toBeInstanceOf(Store::class)
        ->and($response->stores[0]->id)->toBe('store1')
        ->and($response->stores[1]->name)->toBe('Store Two')
        ->and($response->continuationToken)->toBe($mockResponse['continuation_token']);

    $this->assertLastRequest('GET', '/stores');
});

it('lists stores with pagination options', function () {
    $mockResponse = [
        'stores' => [
            ['id' => 'store3', 'name' => 'Store Three', 'created_at' => '2024-01-03T12:00:00Z', 'updated_at' => '2024-01-03T12:00:00Z'],
        ],
        'continuation_token' => ''
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $options = new ListStoresRequestOptions(
        pageSize: 5,
        continuationToken: bin2hex(random_bytes(16))
    );
    $response = $this->client->listStores($options);

    expect($response)->toBeInstanceOf(ListStoresResponse::class)
        ->and($response->stores)->toHaveCount(1)
        ->and($response->continuationToken)->toBeEmpty();

    $this->assertLastRequest('GET', '/stores');
    $this->assertLastRequestQueryContains('page_size', '5');
    $this->assertLastRequestQueryContains('continuation_token', $options->continuationToken);
});

it('creates a store successfully', function () {
    $storeName = 'My New Store';
    $mockResponse = [
        'id' => 'new_store_id',
        'name' => $storeName,
        'created_at' => '2024-01-04T13:00:00Z',
        'updated_at' => '2024-01-04T13:00:00Z',
    ];
    $this->mockHttpResponse(201, $mockResponse);

    $response = $this->client->createStore($storeName);

    expect($response)->toBeInstanceOf(CreateStoreResponse::class)
        ->and($response->id)->toBe('new_store_id')
        ->and($response->name)->toBe($storeName);

    $this->assertLastRequest('POST', '/stores', ['name' => $storeName]);
});

it('gets a store successfully', function () {
    $storeId = 'specific_store_id';
    $mockResponse = [
        'id' => $storeId,
        'name' => 'Specific Store',
        'created_at' => '2024-01-05T14:00:00Z',
        'updated_at' => '2024-01-05T14:00:00Z',
    ];
    $this->mockHttpResponse(200, $mockResponse);

    $response = $this->client->getStore($storeId);

    expect($response)->toBeInstanceOf(GetStoreResponse::class)
        ->and($response->store->id)->toBe($storeId)
        ->and($response->store->name)->toBe('Specific Store');

    $this->assertLastRequest('GET', '/stores/' . $storeId);
});

it('deletes a store successfully', function () {
    $storeIdToDelete = 'store_to_delete';
    $this->mockHttpResponse(204);

    $this->client->deleteStore($storeIdToDelete);

    $this->assertLastRequest('DELETE', '/stores/' . $storeIdToDelete);
});
