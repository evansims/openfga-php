<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use DateTimeImmutable;
use DateTimeInterface;
use OpenFGA\Models\Collections\{Stores, StoresInterface};
use OpenFGA\Models\Store;
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('Stores Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new Stores;

        expect($collection)->toBeInstanceOf(StoresInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new Stores;

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of stores', function (): void {
        $now = new DateTimeImmutable;

        $store1 = new Store(
            id: 'store-1',
            name: 'Production Store',
            createdAt: $now->modify('-10 days'),
            updatedAt: $now->modify('-2 days'),
        );

        $store2 = new Store(
            id: 'store-2',
            name: 'Development Store',
            createdAt: $now->modify('-5 days'),
            updatedAt: $now->modify('-1 day'),
        );

        $store3 = new Store(
            id: 'store-3',
            name: 'Test Store',
            createdAt: $now->modify('-3 days'),
            updatedAt: $now,
        );

        $collection = new Stores([$store1, $store2, $store3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds stores', function (): void {
        $collection = new Stores;
        $now = new DateTimeImmutable;

        $store = new Store(
            id: 'store-123',
            name: 'My Store',
            createdAt: $now,
            updatedAt: $now,
        );

        $collection->add($store);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($store);
    });

    test('checks if store exists', function (): void {
        $now = new DateTimeImmutable;
        $store = new Store(
            id: 'store-456',
            name: 'Another Store',
            createdAt: $now,
            updatedAt: $now,
        );
        $collection = new Stores([$store]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over stores', function (): void {
        $now = new DateTimeImmutable;
        $store1 = new Store(
            id: 'store-1',
            name: 'Store One',
            createdAt: $now,
            updatedAt: $now,
        );
        $store2 = new Store(
            id: 'store-2',
            name: 'Store Two',
            createdAt: $now,
            updatedAt: $now,
        );
        $store3 = new Store(
            id: 'store-3',
            name: 'Store Three',
            createdAt: $now,
            updatedAt: $now,
        );

        $collection = new Stores([$store1, $store2, $store3]);

        $ids = [];
        $names = [];

        foreach ($collection as $store) {
            $ids[] = $store->getId();
            $names[] = $store->getName();
        }

        expect($ids)->toBe(['store-1', 'store-2', 'store-3']);
        expect($names)->toBe(['Store One', 'Store Two', 'Store Three']);
    });

    test('toArray', function (): void {
        $now = new DateTimeImmutable;
        $store1 = new Store(
            id: 'store-a',
            name: 'Store A',
            createdAt: $now,
            updatedAt: $now,
        );
        $store2 = new Store(
            id: 'store-b',
            name: 'Store B',
            createdAt: $now,
            updatedAt: $now,
        );

        $collection = new Stores([$store1, $store2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($store1);
        expect($array[1])->toBe($store2);
    });

    test('jsonSerialize', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-15 10:00:00');
        $updatedAt = new DateTimeImmutable('2024-01-16 15:30:00');

        $store1 = new Store(
            id: 'store-1',
            name: 'First Store',
            createdAt: $createdAt,
            updatedAt: $createdAt,
        );
        $store2 = new Store(
            id: 'store-2',
            name: 'Second Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $collection = new Stores([$store1, $store2]);
        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(2);

        expect($json[0])->toBe([
            'id' => 'store-1',
            'name' => 'First Store',
            'created_at' => $createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $createdAt->format(DateTimeInterface::ATOM),
        ]);

        expect($json[1])->toBe([
            'id' => 'store-2',
            'name' => 'Second Store',
            'created_at' => $createdAt->format(DateTimeInterface::ATOM),
            'updated_at' => $updatedAt->format(DateTimeInterface::ATOM),
        ]);
    });

    test('schema', function (): void {
        $schema = Stores::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Stores::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Stores::schema();
        $schema2 = Stores::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema1)->toBe($schema2);
    });

    test('filters stores by creation date', function (): void {
        $oldDate = new DateTimeImmutable('2023-01-01');
        $recentDate = new DateTimeImmutable('2024-06-01');
        $newDate = new DateTimeImmutable('2024-12-01');

        $collection = new Stores([
            new Store(
                id: 'old-store',
                name: 'Old Store',
                createdAt: $oldDate,
                updatedAt: $oldDate,
            ),
            new Store(
                id: 'recent-store',
                name: 'Recent Store',
                createdAt: $recentDate,
                updatedAt: $recentDate,
            ),
            new Store(
                id: 'new-store',
                name: 'New Store',
                createdAt: $newDate,
                updatedAt: $newDate,
            ),
        ]);

        // Filter stores created in 2024
        $stores2024 = [];
        $cutoffDate = new DateTimeImmutable('2024-01-01');

        foreach ($collection as $store) {
            if ($store->getCreatedAt() >= $cutoffDate) {
                $stores2024[] = $store->getId();
            }
        }

        expect($stores2024)->toBe(['recent-store', 'new-store']);
    });

    test('finds most recently updated store', function (): void {
        $date1 = new DateTimeImmutable('2024-01-01');
        $date2 = new DateTimeImmutable('2024-01-10');
        $date3 = new DateTimeImmutable('2024-01-15');
        $date4 = new DateTimeImmutable('2024-01-05');

        $collection = new Stores([
            new Store(
                id: 'store-1',
                name: 'Store 1',
                createdAt: $date1,
                updatedAt: $date2,
            ),
            new Store(
                id: 'store-2',
                name: 'Store 2',
                createdAt: $date1,
                updatedAt: $date3,
            ),
            new Store(
                id: 'store-3',
                name: 'Store 3',
                createdAt: $date1,
                updatedAt: $date4,
            ),
        ]);

        $mostRecent = null;
        $mostRecentDate = null;

        foreach ($collection as $store) {
            if (null === $mostRecentDate || $store->getUpdatedAt() > $mostRecentDate) {
                $mostRecent = $store;
                $mostRecentDate = $store->getUpdatedAt();
            }
        }

        expect($mostRecent)->not->toBeNull();
        expect($mostRecent->getId())->toBe('store-2');
    });

    test('handles different store naming patterns', function (): void {
        $now = new DateTimeImmutable;
        $collection = new Stores([
            new Store(id: '01HXF7M9KT', name: 'Production Environment', createdAt: $now, updatedAt: $now),
            new Store(id: '01HXF7M9KU', name: 'Staging Environment', createdAt: $now, updatedAt: $now),
            new Store(id: '01HXF7M9KV', name: 'Development Environment', createdAt: $now, updatedAt: $now),
            new Store(id: '01HXF7M9KW', name: 'QA Environment', createdAt: $now, updatedAt: $now),
            new Store(id: '01HXF7M9KX', name: 'Demo Environment', createdAt: $now, updatedAt: $now),
        ]);

        expect($collection->count())->toBe(5);

        // Find environments containing 'Environment'
        $environmentStores = [];

        foreach ($collection as $store) {
            if (str_contains($store->getName(), 'Environment')) {
                $environmentStores[] = $store->getName();
            }
        }

        expect($environmentStores)->toHaveCount(5);
    });

    test('handles stores with deleted dates', function (): void {
        $createdAt = new DateTimeImmutable('2024-01-01');
        $updatedAt = new DateTimeImmutable('2024-01-15');
        $deletedAt = new DateTimeImmutable('2024-01-20');

        $activeStore = new Store(
            id: 'active-store',
            name: 'Active Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );

        $deletedStore = new Store(
            id: 'deleted-store',
            name: 'Deleted Store',
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            deletedAt: $deletedAt,
        );

        $collection = new Stores([$activeStore, $deletedStore]);

        // Count active stores
        $activeCount = 0;

        foreach ($collection as $store) {
            if (null === $store->getDeletedAt()) {
                ++$activeCount;
            }
        }

        expect($activeCount)->toBe(1);

        // Verify deleted store has deletedAt
        expect($deletedStore->getDeletedAt())->toBe($deletedAt);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Stores;

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $item) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get(0))->toBeNull();
    });
});
