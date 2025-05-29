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
});

test('creates and deletes a store', function (): void {
    $store = null;

    $name = 'php-sdk-test-' . bin2hex(random_bytes(5));

    $store = ($this->client->createStore(name: $name))
        ->rethrow()
        ->unwrap();

    expect($store->getId())->not()->toBe('');
    expect($store->getName())->toBe($name);

    $delete = $this->client->deleteStore(store: $store->getId());
    expect($delete->succeeded())->toBeTrue();
});

test('retrieves a created store', function (): void {
    $createdStoreId = null;

    $name = 'php-sdk-test-' . bin2hex(random_bytes(5));

    $create = ($this->client->createStore(name: $name))->rethrow()->unwrap();
    $createdStoreId = $create->getId();

    $get = ($this->client->getStore(store: $createdStoreId))->rethrow()->unwrap();
    expect($get->getStore()->getName())->toBe($name);

    // Verify we can list stores (even if pagination means we don't see our specific store)
    $list = ($this->client->listStores(pageSize: 10))
        ->rethrow()
        ->unwrap();

    // Just verify that listing works and returns some stores
    expect($list->getStores()->count())->toBeGreaterThan(0);

    // If there's a continuation token, it means there are more stores
    if (null === $list->getContinuationToken()) {
        $ids = [];
        foreach ($list->getStores() as $store) {
            $ids[] = $store->getId();
        }
        expect($ids)->toContain($createdStoreId);
    }

    $delete = $this->client->deleteStore(store: $createdStoreId);
    expect($delete->succeeded())->toBeTrue();
});
