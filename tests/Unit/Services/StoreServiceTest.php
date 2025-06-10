<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use Exception;
use OpenFGA\Models\Collections\Stores;
use OpenFGA\Models\Store;
use OpenFGA\Repositories\StoreRepositoryInterface;
use OpenFGA\Responses\ListStoresResponse;
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Services\StoreService;

beforeEach(function (): void {
    $this->mockRepository = test()->createMock(StoreRepositoryInterface::class);
    $this->service = new StoreService($this->mockRepository);
});

describe('createStore', function (): void {
    it('validates store name is not empty', function (): void {
        $result = $this->service->createStore('');

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->unwrap(fn () => null))->toBeNull();
    });

    it('validates store name is not too long', function (): void {
        $longName = str_repeat('a', 257);

        $result = $this->service->createStore($longName);

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->unwrap(fn () => null))->toBeNull();
    });

    it('trims whitespace from store name', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $this->mockRepository
            ->expects(test()->once())
            ->method('create')
            ->with('Test Store')
            ->willReturn(new Success($store));

        $result = $this->service->createStore('  Test Store  ');

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->unwrap()->getName())->toBe('Test Store');
    });

    it('creates store with valid name', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $this->mockRepository
            ->expects(test()->once())
            ->method('create')
            ->with('Test Store')
            ->willReturn(new Success($store));

        $result = $this->service->createStore('Test Store');

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->unwrap())->toBe($store);
    });
});

describe('findStore', function (): void {
    it('enhances 404 error messages', function (): void {
        $error = new Exception('Error: 404 Not Found');

        $this->mockRepository
            ->expects(test()->once())
            ->method('get')
            ->with('store-123')
            ->willReturn(new Failure($error));

        $result = $this->service->findStore('store-123');

        expect($result)->toBeInstanceOf(Failure::class);

        $result->failure(function ($error): void {
            expect($error->getMessage())->toContain('was not found');
        });
    });

    it('preserves other errors', function (): void {
        $error = new Exception('Network timeout');

        $this->mockRepository
            ->expects(test()->once())
            ->method('get')
            ->with('store-123')
            ->willReturn(new Failure($error));

        $result = $this->service->findStore('store-123');

        expect($result)->toBeInstanceOf(Failure::class);

        $result->failure(function ($error): void {
            expect($error->getMessage())->toBe('Network timeout');
        });
    });

    it('returns store when found', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $this->mockRepository
            ->expects(test()->once())
            ->method('get')
            ->with('store-123')
            ->willReturn(new Success($store));

        $result = $this->service->findStore('store-123');

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->unwrap())->toBe($store);
    });
});

describe('listAllStores', function (): void {
    it('handles paginated results', function (): void {
        $stores1 = new Stores;
        $stores1->add(new Store(
            id: 'store-1',
            name: 'Store 1',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $response1 = new ListStoresResponse($stores1, 'token-1');

        $stores2 = new Stores;
        $stores2->add(new Store(
            id: 'store-2',
            name: 'Store 2',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $response2 = new ListStoresResponse($stores2, null);

        $this->mockRepository
            ->expects(test()->exactly(2))
            ->method('list')
            ->willReturnOnConsecutiveCalls(
                new Success($response1),
                new Success($response2),
            );

        $result = $this->service->listAllStores();

        expect($result)->toBeInstanceOf(Success::class);

        $allStores = $result->unwrap();
        expect($allStores)->toHaveCount(2);
    });

    it('respects max items limit', function (): void {
        $stores = new Stores;

        for ($i = 1; 5 >= $i; $i++) {
            $stores->add(new Store(
                id: "store-{$i}",
                name: "Store {$i}",
                createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
                updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            ));
        }

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $result = $this->service->listAllStores(3);

        expect($result)->toBeInstanceOf(Success::class);

        $limitedStores = $result->unwrap();
        expect($limitedStores)->toHaveCount(3);
    });
});

describe('deleteStore', function (): void {
    it('confirms store exists before deletion when enabled', function (): void {
        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $this->mockRepository
            ->expects(test()->once())
            ->method('get')
            ->with('store-123')
            ->willReturn(new Success($store));

        $this->mockRepository
            ->expects(test()->once())
            ->method('delete')
            ->with('store-123')
            ->willReturn(new Success(null));

        $result = $this->service->deleteStore('store-123', true);

        expect($result)->toBeInstanceOf(Success::class);
    });

    it('skips confirmation when disabled', function (): void {
        $this->mockRepository
            ->expects(test()->never())
            ->method('get');

        $this->mockRepository
            ->expects(test()->once())
            ->method('delete')
            ->with('store-123')
            ->willReturn(new Success(null));

        $result = $this->service->deleteStore('store-123', false);

        expect($result)->toBeInstanceOf(Success::class);
    });

    it('returns failure if store not found during confirmation', function (): void {
        $error = new Exception('Error: 404 Not Found');

        $this->mockRepository
            ->expects(test()->once())
            ->method('get')
            ->with('store-123')
            ->willReturn(new Failure($error));

        $this->mockRepository
            ->expects(test()->never())
            ->method('delete');

        $result = $this->service->deleteStore('store-123', true);

        expect($result)->toBeInstanceOf(Failure::class);
    });
});

describe('getOrCreateStore', function (): void {
    it('returns existing store with matching name', function (): void {
        $existingStore = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $stores = new Stores;
        $stores->add($existingStore);
        $stores->add(new Store(
            id: 'store-456',
            name: 'Other Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $this->mockRepository
            ->expects(test()->never())
            ->method('create');

        $result = $this->service->getOrCreateStore('Test Store');

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->unwrap()->getId())->toBe('store-123');
    });

    it('creates new store if none match', function (): void {
        $stores = new Stores;
        $stores->add(new Store(
            id: 'store-456',
            name: 'Other Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $newStore = new Store(
            id: 'store-789',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        );

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $this->mockRepository
            ->expects(test()->once())
            ->method('create')
            ->with('Test Store')
            ->willReturn(new Success($newStore));

        $result = $this->service->getOrCreateStore('Test Store');

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->unwrap()->getId())->toBe('store-789');
    });
});

describe('findStoresByName', function (): void {
    it('finds stores matching pattern', function (): void {
        $stores = new Stores;
        $stores->add(new Store(
            id: 'store-1',
            name: 'Test Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));
        $stores->add(new Store(
            id: 'store-2',
            name: 'Test Store 2',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));
        $stores->add(new Store(
            id: 'store-3',
            name: 'Production Store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $result = $this->service->findStoresByName('Test*');

        expect($result)->toBeInstanceOf(Success::class);

        $matches = $result->unwrap();
        expect($matches)->toHaveCount(2);
        expect($matches->toArray()[0]->getName())->toBe('Test Store');
        expect($matches->toArray()[1]->getName())->toBe('Test Store 2');
    });

    it('respects max items when finding stores', function (): void {
        $stores = new Stores;

        for ($i = 1; 5 >= $i; $i++) {
            $stores->add(new Store(
                id: "store-{$i}",
                name: "Test Store {$i}",
                createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
                updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            ));
        }

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $result = $this->service->findStoresByName('Test*', 2);

        expect($result)->toBeInstanceOf(Success::class);

        $matches = $result->unwrap();
        expect($matches)->toHaveCount(2);
    });

    it('performs case-insensitive matching', function (): void {
        $stores = new Stores;
        $stores->add(new Store(
            id: 'store-1',
            name: 'TEST STORE',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));
        $stores->add(new Store(
            id: 'store-2',
            name: 'test store',
            createdAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
            updatedAt: new DateTimeImmutable('2024-01-01T00:00:00Z'),
        ));

        $this->mockRepository
            ->expects(test()->once())
            ->method('list')
            ->with(null)
            ->willReturn(new Success(new ListStoresResponse($stores)));

        $result = $this->service->findStoresByName('test*');

        expect($result)->toBeInstanceOf(Success::class);

        $matches = $result->unwrap();
        expect($matches)->toHaveCount(2);
    });
});
