<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use DateTimeImmutable;
use OpenFGA\Models\{Store, Stores, StoreInterface, StoresInterface};

it('can create a Store instance with constructor', function () {
    $id = 'store123';
    $name = 'Test Store';
    $createdAt = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T12:00:00+00:00');

    $store = new Store($id, $name, $createdAt, $updatedAt);

    expect($store)->toBeInstanceOf(StoreInterface::class)
        ->and($store->id)->toBe($id)
        ->and($store->name)->toBe($name)
        ->and($store->createdAt)->toBe($createdAt)
        ->and($store->updatedAt)->toBe($updatedAt)
        ->and($store->deletedAt)->toBeNull();
});

it('can create a Store instance with optional deletedAt', function () {
    $id = 'store123';
    $name = 'Test Store';
    $createdAt = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T12:00:00+00:00');
    $deletedAt = new DateTimeImmutable('2023-01-03T12:00:00+00:00');

    $store = new Store($id, $name, $createdAt, $updatedAt, $deletedAt);

    expect($store->deletedAt)->toBe($deletedAt);
});

it('can convert Store to array', function () {
    $id = 'store123';
    $name = 'Test Store';
    $createdAt = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T12:00:00+00:00');

    $store = new Store($id, $name, $createdAt, $updatedAt);
    $array = $store->toArray();

    expect($array)->toBeArray()
        ->and($array['id'])->toBe($id)
        ->and($array['name'])->toBe($name)
        ->and($array['created_at'])->toBe('2023-01-01T12:00:00+00:00')
        ->and($array['updated_at'])->toBe('2023-01-02T12:00:00+00:00')
        ->and($array['deleted_at'])->toBeNull();
});

it('can convert Store with deletedAt to array', function () {
    $id = 'store123';
    $name = 'Test Store';
    $createdAt = new DateTimeImmutable('2023-01-01T12:00:00+00:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T12:00:00+00:00');
    $deletedAt = new DateTimeImmutable('2023-01-03T12:00:00+00:00');

    $store = new Store($id, $name, $createdAt, $updatedAt, $deletedAt);
    $array = $store->toArray();

    expect($array['deleted_at'])->toBe('2023-01-03T12:00:00+00:00');
});

it('can create Store from array', function () {
    $data = [
        'id' => 'store123',
        'name' => 'Test Store',
        'created_at' => '2023-01-01T12:00:00+00:00',
        'updated_at' => '2023-01-02T12:00:00+00:00',
    ];

    $store = Store::fromArray($data);

    expect($store)->toBeInstanceOf(StoreInterface::class)
        ->and($store->id)->toBe($data['id'])
        ->and($store->name)->toBe($data['name'])
        ->and($store->createdAt->format('Y-m-d\TH:i:sP'))->toBe($data['created_at'])
        ->and($store->updatedAt->format('Y-m-d\TH:i:sP'))->toBe($data['updated_at'])
        ->and($store->deletedAt)->toBeNull();
});

it('can create Store from array with deletedAt', function () {
    $data = [
        'id' => 'store123',
        'name' => 'Test Store',
        'created_at' => '2023-01-01T12:00:00+00:00',
        'updated_at' => '2023-01-02T12:00:00+00:00',
        'deleted_at' => '2023-01-03T12:00:00+00:00',
    ];

    $store = Store::fromArray($data);

    expect($store->deletedAt->format('Y-m-d\TH:i:sP'))->toBe($data['deleted_at']);
});

it('can create an empty Stores collection', function () {
    $stores = new Stores();

    expect($stores)->toBeInstanceOf(StoresInterface::class)
        ->and($stores)->toHaveCount(0);
});

it('can add Store to Stores collection', function () {
    $store1 = new Store(
        'store1',
        'Test Store 1',
        new DateTimeImmutable('2023-01-01T12:00:00+00:00'),
        new DateTimeImmutable('2023-01-02T12:00:00+00:00')
    );

    $store2 = new Store(
        'store2',
        'Test Store 2',
        new DateTimeImmutable('2023-01-01T12:00:00+00:00'),
        new DateTimeImmutable('2023-01-02T12:00:00+00:00')
    );

    $stores = new Stores();
    $stores->add($store1);
    $stores->add($store2);

    expect($stores)->toHaveCount(2);
});

it('can get current Store from Stores collection', function () {
    $store = new Store(
        'store1',
        'Test Store 1',
        new DateTimeImmutable('2023-01-01T12:00:00+00:00'),
        new DateTimeImmutable('2023-01-02T12:00:00+00:00')
    );

    $stores = new Stores();
    $stores->add($store);

    expect($stores->current())->toBe($store);
});

it('can get Store by offset from Stores collection', function () {
    $store1 = new Store(
        'store1',
        'Test Store 1',
        new DateTimeImmutable('2023-01-01T12:00:00+00:00'),
        new DateTimeImmutable('2023-01-02T12:00:00+00:00')
    );

    $store2 = new Store(
        'store2',
        'Test Store 2',
        new DateTimeImmutable('2023-01-01T12:00:00+00:00'),
        new DateTimeImmutable('2023-01-02T12:00:00+00:00')
    );

    $stores = new Stores();
    $stores->add($store1);
    $stores->add($store2);

    expect($stores->offsetGet(0))->toBe($store1)
        ->and($stores->offsetGet(1))->toBe($store2)
        ->and($stores->offsetGet(2))->toBeNull();
});

it('can create Stores collection from array', function () {
    $data = [
        [
            'id' => 'store1',
            'name' => 'Test Store 1',
            'created_at' => '2023-01-01T12:00:00+00:00',
            'updated_at' => '2023-01-02T12:00:00+00:00'
        ],
        [
            'id' => 'store2',
            'name' => 'Test Store 2',
            'created_at' => '2023-01-03T12:00:00+00:00',
            'updated_at' => '2023-01-04T12:00:00+00:00',
            'deleted_at' => '2023-01-05T12:00:00+00:00'
        ]
    ];

    $stores = Stores::fromArray($data);

    expect($stores)->toBeInstanceOf(StoresInterface::class)
        ->and($stores)->toHaveCount(2)
        ->and($stores->offsetGet(0)->id)->toBe('store1')
        ->and($stores->offsetGet(0)->name)->toBe('Test Store 1')
        ->and($stores->offsetGet(0)->deletedAt)->toBeNull()
        ->and($stores->offsetGet(1)->id)->toBe('store2')
        ->and($stores->offsetGet(1)->name)->toBe('Test Store 2')
        ->and($stores->offsetGet(1)->deletedAt)->not->toBeNull();
});
