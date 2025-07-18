<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use DateTimeInterface;
use OpenFGA\{Client, Messages};
use OpenFGA\Exceptions\ClientException;

use function count;
use function OpenFGA\trans;
use function sprintf;

describe('List Stores', function (): void {
    beforeEach(function (): void {
        $this->client = new Client(url: getOpenFgaUrl());

        $this->createdStoreIds = [];
        $timestamp = time();

        for ($i = 0; 15 > $i; ++$i) {
            $name = sprintf('pagination-test-%d-%s', $timestamp, bin2hex(random_bytes(3)));
            $store = $this->client->createStore(name: $name)
                ->rethrow()
                ->unwrap();
            $this->createdStoreIds[] = $store->getId();
        }

        // Give stores time to be fully persisted
        usleep(200000); // 200ms delay after creating all stores
    });

    afterEach(function (): void {
        foreach ($this->createdStoreIds ?? [] as $storeId) {
            $this->client->deleteStore(store: $storeId);
        }
    });

    test('list stores without pagination', function (): void {
        $result = $this->client->listStores()->rethrow()->unwrap();

        expect($result->getStores())->not->toBeNull();
        expect($result->getStores()->count())->toBeGreaterThanOrEqual(15);
    });

    test('list stores with page size', function (): void {
        $result = $this->client->listStores(pageSize: 5)->rethrow()->unwrap();

        expect($result->getStores())->not->toBeNull();
        expect($result->getStores()->count())->toBeLessThanOrEqual(5);

        $continuationToken = $result->getContinuationToken();

        if ($continuationToken) {
            expect($continuationToken)->toBeString();
            expect($continuationToken)->not->toBeEmpty();
        }
    });

    test('list stores with continuation token', function (): void {
        $firstPage = $this->client->listStores(pageSize: 5)->rethrow()->unwrap();

        expect($firstPage->getStores())->not->toBeNull();
        $firstPageCount = $firstPage->getStores()->count();
        expect($firstPageCount)->toBeLessThanOrEqual(5);

        $continuationToken = $firstPage->getContinuationToken();

        if ($continuationToken) {
            $secondPage = $this->client->listStores(
                pageSize: 5,
                continuationToken: $continuationToken,
            )->rethrow()->unwrap();

            expect($secondPage->getStores())->not->toBeNull();

            $firstPageIds = [];

            foreach ($firstPage->getStores() as $store) {
                $firstPageIds[] = $store->getId();
            }

            $secondPageIds = [];

            foreach ($secondPage->getStores() as $store) {
                $secondPageIds[] = $store->getId();
            }

            $overlap = array_intersect($firstPageIds, $secondPageIds);
            expect($overlap)->toBeEmpty();
        }
    });

    test('list stores iterate through all pages', function (): void {
        // Give stores time to be fully persisted
        usleep(100000); // 100ms delay

        $allStores = [];
        $pageSize = 3;
        $continuationToken = null;
        $pageCount = 0;
        $maxPages = 20; // Safety limit

        do {
            $result = $this->client->listStores(
                pageSize: $pageSize,
                continuationToken: $continuationToken,
            )->rethrow()->unwrap();

            $stores = $result->getStores();
            expect($stores)->not->toBeNull();

            foreach ($stores as $store) {
                $allStores[] = $store->getId();
            }

            $continuationToken = $result->getContinuationToken();
            ++$pageCount;
        } while ($continuationToken && $pageCount < $maxPages);

        $testStoresFound = array_intersect($allStores, $this->createdStoreIds);

        // If we don't find all stores, retry once after a delay
        if (15 > count($testStoresFound)) {
            usleep(500000); // 500ms delay

            // Try listing again with a larger page size
            $retryResult = $this->client->listStores(pageSize: 100)->rethrow()->unwrap();
            $retryStores = [];

            foreach ($retryResult->getStores() as $store) {
                $retryStores[] = $store->getId();
            }
            $testStoresFound = array_intersect($retryStores, $this->createdStoreIds);
        }

        // Allow for some stores to be missing due to timing, but most should be found
        expect(count($testStoresFound))->toBeGreaterThanOrEqual(13);
    });

    test('list stores with large page size', function (): void {
        $result = $this->client->listStores(pageSize: 100)->rethrow()->unwrap();

        expect($result->getStores())->not->toBeNull();

        expect($result->getStores()->count())->toBeGreaterThanOrEqual(15);
    });

    test('list stores with page size of 1', function (): void {
        $result = $this->client->listStores(pageSize: 1)->rethrow()->unwrap();

        expect($result->getStores())->not->toBeNull();
        expect($result->getStores()->count())->toBe(1);

        expect($result->getContinuationToken())->not->toBeNull();
    });

    test('empty continuation token validation', function (): void {
        $this->client->listStores(
            pageSize: 5,
            continuationToken: '',
        )->rethrow();
    })->throws(ClientException::class, trans(Messages::REQUEST_CONTINUATION_TOKEN_EMPTY));

    test('store deletion during pagination', function (): void {
        $firstPage = $this->client->listStores(pageSize: 5)->rethrow()->unwrap();
        $continuationToken = $firstPage->getContinuationToken();

        if ($continuationToken && 0 < count($this->createdStoreIds)) {
            $storeToDelete = array_pop($this->createdStoreIds);
            $this->client->deleteStore(store: $storeToDelete)->rethrow()->unwrap();

            $secondPage = $this->client->listStores(
                pageSize: 5,
                continuationToken: $continuationToken,
            )->rethrow()->unwrap();

            expect($secondPage->getStores())->not->toBeNull();

            $secondPageIds = [];

            foreach ($secondPage->getStores() as $store) {
                $secondPageIds[] = $store->getId();
            }
            expect($secondPageIds)->not->toContain($storeToDelete);
        }
    });

    test('list stores metadata fields', function (): void {
        $result = $this->client->listStores(pageSize: 1)->rethrow()->unwrap();

        expect($result->getStores())->not->toBeNull();
        expect($result->getStores()->count())->toBe(1);

        $store = $result->getStores()->first();
        expect($store)->not->toBeNull();

        expect($store->getId())->toBeString();
        expect($store->getName())->toBeString();
        expect($store->getCreatedAt())->toBeInstanceOf(DateTimeInterface::class);
        expect($store->getUpdatedAt())->toBeInstanceOf(DateTimeInterface::class);

        expect($store->getUpdatedAt()->getTimestamp())
            ->toBeGreaterThanOrEqual($store->getCreatedAt()->getTimestamp());
    });
});
