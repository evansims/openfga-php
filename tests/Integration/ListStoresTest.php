<?php

declare(strict_types=1);

use OpenFGA\Client;

beforeEach(function (): void {
    $this->responseFactory = new Nyholm\Psr7\Factory\Psr17Factory();
    $this->httpClient = new Buzz\Client\FileGetContents($this->responseFactory);
    $this->httpRequestFactory = $this->responseFactory;
    $this->httpStreamFactory = $this->responseFactory;
    $this->url = getenv('FGA_API_URL') ?: 'http://openfga:8080';

    $this->client = new Client(
        url: $this->url,
        httpClient: $this->httpClient,
        httpResponseFactory: $this->responseFactory,
        httpStreamFactory: $this->httpStreamFactory,
        httpRequestFactory: $this->httpRequestFactory,
    );

    // Create multiple test stores for pagination testing
    $this->createdStoreIds = [];
    $timestamp = time();

    for ($i = 0; $i < 15; ++$i) {
        $name = \sprintf('pagination-test-%d-%s', $timestamp, bin2hex(random_bytes(3)));
        $store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->createdStoreIds[] = $store->getId();
    }
});

afterEach(function (): void {
    // Clean up all created stores
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

    // Should have a continuation token if there are more stores
    $continuationToken = $result->getContinuationToken();
    if ($continuationToken) {
        expect($continuationToken)->toBeString();
        expect($continuationToken)->not->toBeEmpty();
    }
});

test('list stores with continuation token', function (): void {
    // First page
    $firstPage = $this->client->listStores(pageSize: 5)->rethrow()->unwrap();

    expect($firstPage->getStores())->not->toBeNull();
    $firstPageCount = $firstPage->getStores()->count();
    expect($firstPageCount)->toBeLessThanOrEqual(5);

    $continuationToken = $firstPage->getContinuationToken();

    if ($continuationToken) {
        // Second page using continuation token
        $secondPage = $this->client->listStores(
            pageSize: 5,
            continuationToken: $continuationToken,
        )->rethrow()->unwrap();

        expect($secondPage->getStores())->not->toBeNull();

        // Store IDs should be different between pages
        $firstPageIds = [];
        foreach ($firstPage->getStores() as $store) {
            $firstPageIds[] = $store->getId();
        }

        $secondPageIds = [];
        foreach ($secondPage->getStores() as $store) {
            $secondPageIds[] = $store->getId();
        }

        // No overlap between pages
        $overlap = array_intersect($firstPageIds, $secondPageIds);
        expect($overlap)->toBeEmpty();
    }
});

test('list stores iterate through all pages', function (): void {
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

    // Should have retrieved all our test stores
    $testStoresFound = array_intersect($allStores, $this->createdStoreIds);
    expect(\count($testStoresFound))->toBe(15);
});

test('list stores with large page size', function (): void {
    $result = $this->client->listStores(pageSize: 100)->rethrow()->unwrap();

    expect($result->getStores())->not->toBeNull();
    // Should get all stores in one page (assuming less than 100 total)
    expect($result->getStores()->count())->toBeGreaterThanOrEqual(15);
});

test('list stores with page size of 1', function (): void {
    $result = $this->client->listStores(pageSize: 1)->rethrow()->unwrap();

    expect($result->getStores())->not->toBeNull();
    expect($result->getStores()->count())->toBe(1);

    // Should definitely have a continuation token
    expect($result->getContinuationToken())->not->toBeNull();
});

test('list stores validates empty continuation token', function (): void {
    // Empty string continuation token should throw an error
    expect(fn () => $this->client->listStores(
        pageSize: 5,
        continuationToken: '',
    ))->toThrow(InvalidArgumentException::class, 'Continuation token cannot be empty');
});

test('list stores handles store deletion during pagination', function (): void {
    // Get first page
    $firstPage = $this->client->listStores(pageSize: 5)->rethrow()->unwrap();
    $continuationToken = $firstPage->getContinuationToken();

    if ($continuationToken && \count($this->createdStoreIds) > 0) {
        // Delete one of our test stores
        $storeToDelete = array_pop($this->createdStoreIds);
        $this->client->deleteStore(store: $storeToDelete)->rethrow()->unwrap();

        // Continue pagination - should still work
        $secondPage = $this->client->listStores(
            pageSize: 5,
            continuationToken: $continuationToken,
        )->rethrow()->unwrap();

        expect($secondPage->getStores())->not->toBeNull();

        // The deleted store should not appear
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

    // Check store has expected fields
    expect($store->getId())->toBeString();
    expect($store->getName())->toBeString();
    expect($store->getCreatedAt())->toBeInstanceOf(DateTimeInterface::class);
    expect($store->getUpdatedAt())->toBeInstanceOf(DateTimeInterface::class);

    // Updated should be same or after created
    expect($store->getUpdatedAt()->getTimestamp())
        ->toBeGreaterThanOrEqual($store->getCreatedAt()->getTimestamp());
});
