<?php

declare(strict_types=1);

use OpenFGA\Models\{Store, StoreInterface};
use OpenFGA\Schema\SchemaInterface;

describe('Store Model', function (): void {
    test('implements StoreInterface', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        expect($store)->toBeInstanceOf(StoreInterface::class);
    });

    test('constructs with required parameters only', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00');

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        expect($store->getId())->toBe('store-123');
        expect($store->getName())->toBe('Test Store');
        expect($store->getCreatedAt())->toBe($createdAt);
        expect($store->getUpdatedAt())->toBe($updatedAt);
        expect($store->getDeletedAt())->toBeNull();
    });

    test('constructs with deletedAt parameter', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00');
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00');
        $deletedAt = new DateTimeImmutable('2023-01-03 12:00:00');

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt,
        );

        expect($store->getDeletedAt())->toBe($deletedAt);
    });

    test('serializes to JSON without deletedAt', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00', new DateTimeZone('UTC'));
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00', new DateTimeZone('UTC'));

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $json = $store->jsonSerialize();

        expect($json)->toBe([
            'id' => 'store-123',
            'name' => 'Test Store',
            'created_at' => '2023-01-01T10:00:00+00:00',
            'updated_at' => '2023-01-02T11:00:00+00:00',
        ]);
        expect($json)->not->toHaveKey('deleted_at');
    });

    test('serializes to JSON with deletedAt', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00', new DateTimeZone('UTC'));
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00', new DateTimeZone('UTC'));
        $deletedAt = new DateTimeImmutable('2023-01-03 12:00:00', new DateTimeZone('UTC'));

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt,
        );

        $json = $store->jsonSerialize();

        expect($json)->toHaveKeys(['id', 'name', 'created_at', 'updated_at', 'deleted_at']);
        expect($json['deleted_at'])->toBe('2023-01-03T12:00:00+00:00');
    });

    test('converts timestamps to UTC', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00', new DateTimeZone('America/New_York'));
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00', new DateTimeZone('Europe/London'));

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $json = $store->jsonSerialize();

        // EST is UTC-5, so 10:00 EST becomes 15:00 UTC
        expect($json['created_at'])->toBe('2023-01-01T15:00:00+00:00');
        // GMT in January, so 11:00 GMT is 11:00 UTC
        expect($json['updated_at'])->toBe('2023-01-02T11:00:00+00:00');
    });

    test('handles DateTimeImmutable objects in UTC', function (): void {
        $createdAt = new DateTimeImmutable('2023-01-01 10:00:00', new DateTimeZone('UTC'));
        $updatedAt = new DateTimeImmutable('2023-01-02 11:00:00', new DateTimeZone('UTC'));

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $json = $store->jsonSerialize();

        expect($json['created_at'])->toBe('2023-01-01T10:00:00+00:00');
        expect($json['updated_at'])->toBe('2023-01-02T11:00:00+00:00');
    });

    test('handles empty store name', function (): void {
        $store = new Store(
            id: 'store-123',
            name: '',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        expect($store->getName())->toBe('');
    });

    test('handles UUID format IDs', function (): void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';

        $store = new Store(
            id: $uuid,
            name: 'Test Store',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        expect($store->getId())->toBe($uuid);
    });

    test('returns schema instance', function (): void {
        $schema = Store::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Store::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(5);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['id', 'name', 'created_at', 'updated_at', 'deleted_at']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Store::schema();
        $properties = $schema->getProperties();

        // Check required properties
        $requiredProps = ['id', 'name', 'created_at', 'updated_at'];
        foreach ($properties as $property) {
            if (\in_array($property->name, $requiredProps, true)) {
                expect($property->required)->toBe(true);
            } else {
                expect($property->required)->toBe(false);
            }
        }

        // Check date-time format
        $dateProps = ['created_at', 'updated_at', 'deleted_at'];
        foreach ($properties as $property) {
            if (\in_array($property->name, $dateProps, true)) {
                expect($property->format)->toBe('datetime');
            }
        }
    });

    test('schema is cached', function (): void {
        $schema1 = Store::schema();
        $schema2 = Store::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves exact store name', function (): void {
        $store = new Store(
            id: 'store-123',
            name: '  Test Store with Spaces  ',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        expect($store->getName())->toBe('  Test Store with Spaces  ');
    });

    test('handles stores with special characters in name', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store™ - Production (2023)',
            createdAt: new DateTimeImmutable(),
            updatedAt: new DateTimeImmutable(),
        );

        expect($store->getName())->toBe('Test Store™ - Production (2023)');
    });
});
