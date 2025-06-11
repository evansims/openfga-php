<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;

describe('Store Management', function (): void {
    beforeEach(function (): void {
        $this->responseFactory = new Psr17Factory;
        $this->httpClient = new FileGetContents($this->responseFactory);
        $this->httpRequestFactory = $this->responseFactory;
        $this->httpStreamFactory = $this->responseFactory;
        $this->url = getOpenFgaUrl();

        $this->client = Client::create(
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

        $store = $this->client->createStore(name: $name)
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

        $create = $this->client->createStore(name: $name)->rethrow()->unwrap();
        $createdStoreId = $create->getId();

        $get = $this->client->getStore(store: $createdStoreId)->rethrow()->unwrap();
        expect($get->getStore()->getName())->toBe($name);

        $list = $this->client->listStores(pageSize: 10)
            ->rethrow()
            ->unwrap();

        expect($list->getStores()->count())->toBeGreaterThan(0);

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
});
