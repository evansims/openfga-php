<?php

declare(strict_types=1);

use OpenFGA\Models\{Store, Stores};

test('empty collection', function (): void {
    $stores = new Stores();

    expect($stores)->toHaveCount(0);
});

test('add store', function (): void {
    $stores = new Stores();
    $store = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'test-store',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores[] = $store;

    expect($stores)->toHaveCount(1)
        ->and($stores[0])->toBe($store);
});

test('create with stores', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    expect($stores)->toHaveCount(2)
        ->and($stores[0])->toBe($store1)
        ->and($stores[1])->toBe($store2);
});

test('json serialize', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2023-01-02T00:00:00+00:00'),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable('2023-01-03T00:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2023-01-04T00:00:00+00:00'),
    );

    $stores = new Stores([$store1, $store2]);

    $result = $stores->jsonSerialize();

    expect($result)->toBe([
        $store1->jsonSerialize(),
        $store2->jsonSerialize(),
    ]);
});

test('filter stores', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    $filtered = $stores->filter(fn (Store $store) => 'store-1' === $store->getName());

    expect($filtered)->toHaveCount(1)
        ->and($filtered[0])->toBe($store1);
});

test('find store by id', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    $found = $stores->first(fn (Store $store) => '01H123456789ABCDEFGHJKLMO' === $store->getId());

    expect($found)->toBe($store2);
});

test('map stores to names', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    $mapped = $stores->map(
        Store::class,
        fn (Store $store) => new Store(
            id: $store->getId(),
            name: strtoupper($store->getName()),
            createdAt: $store->getCreatedAt(),
            updatedAt: $store->getUpdatedAt(),
            deletedAt: $store->getDeletedAt(),
        ),
    );

    expect($mapped)
        ->toBeInstanceOf(Stores::class)
        ->toHaveCount(2)
        ->and($mapped[0])->toBeInstanceOf(Store::class)
        ->and($mapped[0]->getName())->toBe('STORE-1')
        ->and($mapped[1]->getName())->toBe('STORE-2');
});

test('check if any store has name', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    $hasStore1 = $stores->some(fn (Store $store) => 'store-1' === $store->getName());
    $hasStore3 = $stores->some(fn (Store $store) => 'store-3' === $store->getName());

    expect($hasStore1)->toBeTrue()
        ->and($hasStore3)->toBeFalse();
});

test('convert to array', function (): void {
    $store1 = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'store-1',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $store2 = new Store(
        id: '01H123456789ABCDEFGHJKLMO',
        name: 'store-2',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    $stores = new Stores([$store1, $store2]);

    $array = $stores->toArray();

    expect($array)->toBe([$store1, $store2]);
});
