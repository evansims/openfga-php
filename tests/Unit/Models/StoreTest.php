<?php

declare(strict_types=1);

use OpenFGA\Models\Store;

test('constructor and getters', function (): void {
    $id = '01H123456789ABCDEFGHJKLMN';
    $name = 'test-store';
    $createdAt = new DateTimeImmutable('2023-01-01T00:00:00+00:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T00:00:00+00:00');
    $deletedAt = new DateTimeImmutable('2023-01-03T00:00:00+00:00');

    $store = new Store(
        id: $id,
        name: $name,
        createdAt: $createdAt,
        updatedAt: $updatedAt,
        deletedAt: $deletedAt,
    );

    expect($store->getId())->toBe($id)
        ->and($store->getName())->toBe($name)
        ->and($store->getCreatedAt())->toEqual($createdAt)
        ->and($store->getUpdatedAt())->toEqual($updatedAt)
        ->and($store->getDeletedAt())->toEqual($deletedAt);
});

test('optional deletedAt', function (): void {
    $store = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'test-store',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
    );

    expect($store->getDeletedAt())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $id = '01H123456789ABCDEFGHJKLMN';
    $name = 'test-store';
    $createdAt = new DateTimeImmutable('2023-01-01T12:00:00+05:00');
    $updatedAt = new DateTimeImmutable('2023-01-02T12:00:00+05:00');
    $deletedAt = new DateTimeImmutable('2023-01-03T12:00:00+05:00');

    $store = new Store(
        id: $id,
        name: $name,
        createdAt: $createdAt,
        updatedAt: $updatedAt,
        deletedAt: $deletedAt,
    );

    $result = $store->jsonSerialize();

    expect($result)->toBe([
        'id' => $id,
        'name' => $name,
        'created_at' => '2023-01-01T07:00:00+00:00', // Should be converted to UTC
        'updated_at' => '2023-01-02T07:00:00+00:00', // Should be converted to UTC
        'deleted_at' => '2023-01-03T07:00:00+00:00', // Should be converted to UTC
    ]);
});

test('json serialize without deletedAt', function (): void {
    $store = new Store(
        id: '01H123456789ABCDEFGHJKLMN',
        name: 'test-store',
        createdAt: new DateTimeImmutable('2023-01-01T00:00:00+00:00'),
        updatedAt: new DateTimeImmutable('2023-01-02T00:00:00+00:00'),
    );

    $result = $store->jsonSerialize();

    expect($result)->toHaveKeys(['id', 'name', 'created_at', 'updated_at'])
        ->and($result)->not()->toHaveKey('deleted_at');
});

test('schema', function (): void {
    $schema = Store::schema();

    expect($schema->getClassName())->toBe(Store::class)
        ->and($schema->getProperties())->toHaveCount(5)
        ->and($schema->getProperty('id')->name)->toBe('id')
        ->and($schema->getProperty('id')->type)->toBe('string')
        ->and($schema->getProperty('id')->required)->toBeTrue()
        ->and($schema->getProperty('name')->name)->toBe('name')
        ->and($schema->getProperty('name')->type)->toBe('string')
        ->and($schema->getProperty('name')->required)->toBeTrue()
        ->and($schema->getProperty('created_at')->name)->toBe('created_at')
        ->and($schema->getProperty('created_at')->type)->toBe('datetime')
        ->and($schema->getProperty('created_at')->required)->toBeTrue()
        ->and($schema->getProperty('updated_at')->name)->toBe('updated_at')
        ->and($schema->getProperty('updated_at')->type)->toBe('datetime')
        ->and($schema->getProperty('updated_at')->required)->toBeTrue()
        ->and($schema->getProperty('deleted_at')->name)->toBe('deleted_at')
        ->and($schema->getProperty('deleted_at')->type)->toBe('datetime')
        ->and($schema->getProperty('deleted_at')->required)->toBeFalse();
});
