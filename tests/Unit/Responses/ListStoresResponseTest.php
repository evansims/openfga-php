<?php

declare(strict_types=1);

namespace Tests\Unit\Responses;

use DateTimeImmutable;
use OpenFGA\Models\Collections\Stores;
use OpenFGA\Models\{Store};
use OpenFGA\Responses\{ListStoresResponse, ListStoresResponseInterface};
use OpenFGA\Schema\SchemaInterface;

test('ListStoresResponse implements ListStoresResponseInterface', function (): void {
    $stores = new Stores();
    $response = new ListStoresResponse($stores);
    expect($response)->toBeInstanceOf(ListStoresResponseInterface::class);
});

test('ListStoresResponse constructs with stores only', function (): void {
    $store1 = new Store(
        id: 'store-1',
        name: 'First Store',
        createdAt: new DateTimeImmutable('2024-01-01 10:00:00'),
        updatedAt: new DateTimeImmutable('2024-01-01 10:00:00'),
    );

    $store2 = new Store(
        id: 'store-2',
        name: 'Second Store',
        createdAt: new DateTimeImmutable('2024-01-02 10:00:00'),
        updatedAt: new DateTimeImmutable('2024-01-02 10:00:00'),
    );

    $stores = new Stores($store1, $store2);

    $response = new ListStoresResponse($stores);

    expect($response->getStores())->toBe($stores);
    expect($response->getContinuationToken())->toBeNull();
});

test('ListStoresResponse constructs with stores and continuation token', function (): void {
    $stores = new Stores();
    $continuationToken = 'next-page-token-abc';

    $response = new ListStoresResponse($stores, $continuationToken);

    expect($response->getStores())->toBe($stores);
    expect($response->getContinuationToken())->toBe($continuationToken);
});

test('ListStoresResponse handles empty stores collection', function (): void {
    $stores = new Stores();
    $response = new ListStoresResponse($stores);

    expect($response->getStores())->toBe($stores);
    expect($response->getStores()->count())->toBe(0);
});

test('ListStoresResponse handles large stores collection', function (): void {
    $stores = new Stores();

    for ($i = 1; $i <= 25; ++$i) {
        $store = new Store(
            id: "store-{$i}",
            name: "Store Number {$i}",
            createdAt: new DateTimeImmutable("2024-01-{$i} 10:00:00"),
            updatedAt: new DateTimeImmutable("2024-01-{$i} 10:00:00"),
        );

        $stores->add($store);
    }

    $response = new ListStoresResponse($stores, 'pagination-token-xyz');

    expect($response->getStores()->count())->toBe(25);
    expect($response->getContinuationToken())->toBe('pagination-token-xyz');
});

test('ListStoresResponse handles stores with deletedAt', function (): void {
    $activeStore = new Store(
        id: 'active-store',
        name: 'Active Store',
        createdAt: new DateTimeImmutable('2024-01-01 10:00:00'),
        updatedAt: new DateTimeImmutable('2024-01-01 10:00:00'),
    );

    $deletedStore = new Store(
        id: 'deleted-store',
        name: 'Deleted Store',
        createdAt: new DateTimeImmutable('2024-01-01 10:00:00'),
        updatedAt: new DateTimeImmutable('2024-01-02 10:00:00'),
        deletedAt: new DateTimeImmutable('2024-01-02 10:00:00'),
    );

    $stores = new Stores($activeStore, $deletedStore);
    $response = new ListStoresResponse($stores);

    expect($response->getStores()->count())->toBe(2);

    // Access stores by iteration
    $storeArray = [];
    foreach ($response->getStores() as $store) {
        $storeArray[] = $store;
    }

    expect($storeArray[0]->getDeletedAt())->toBeNull();
    expect($storeArray[1]->getDeletedAt())->not->toBeNull();
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('ListStoresResponse schema returns expected structure', function (): void {
    $schema = ListStoresResponse::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(ListStoresResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(2);

    expect($properties)->toHaveKeys(['stores', 'continuation_token']);

    expect($properties['stores']->name)->toBe('stores');
    expect($properties['stores']->type)->toBe('object');
    expect($properties['stores']->className)->toBe(Stores::class);
    expect($properties['stores']->required)->toBeTrue();

    expect($properties['continuation_token']->name)->toBe('continuation_token');
    expect($properties['continuation_token']->type)->toBe('string');
    expect($properties['continuation_token']->required)->toBeFalse();
});

test('ListStoresResponse schema is cached', function (): void {
    $schema1 = ListStoresResponse::schema();
    $schema2 = ListStoresResponse::schema();

    expect($schema1)->toBe($schema2);
});

test('ListStoresResponse handles empty continuation token', function (): void {
    $stores = new Stores();
    $response = new ListStoresResponse($stores, '');

    expect($response->getContinuationToken())->toBe('');
});

test('ListStoresResponse handles stores with various timestamps', function (): void {
    $store1 = new Store(
        id: 'store-micro',
        name: 'Microsecond Store',
        createdAt: new DateTimeImmutable('2024-01-01 10:00:00.123456'),
        updatedAt: new DateTimeImmutable('2024-01-01 10:00:00.654321'),
    );

    $stores = new Stores($store1);
    $response = new ListStoresResponse($stores);

    expect($response->getStores()->count())->toBe(1);

    $storeArray = [];
    foreach ($response->getStores() as $store) {
        $storeArray[] = $store;
    }

    expect($storeArray[0]->getCreatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.123456');
    expect($storeArray[0]->getUpdatedAt()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.654321');
});
