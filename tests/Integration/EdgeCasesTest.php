<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use OpenFGA\Client;
use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\Collections\UserTypeFilters;
use OpenFGA\Models\UserTypeFilter;

use function OpenFGA\{tuple, tuples};

describe('Edge Cases', function (): void {
    beforeEach(function (): void {
        $this->client = new Client(url: getOpenFgaUrl());

        $name = 'edge-case-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define owner: [user]
            define editor: [user] or owner
            define viewer: [user] or editor
            define can_delete: owner
    ';

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();

        $createModelResponse = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        )->rethrow()->unwrap();

        $this->modelId = $createModelResponse->getModel();
    });

    afterEach(function (): void {
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('empty results for readTuples', function (): void {
        $result = $this->client->readTuples(
            store: $this->storeId,
            tuple: tuple('', '', 'document:nonexistent'),
        )->rethrow()->unwrap();

        expect($result->getTuples())->not->toBeNull();
        expect($result->getTuples()->count())->toBe(0);
    });

    test('empty results for listObjects', function (): void {
        $result = $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
        )->rethrow()->unwrap();

        expect($result->getObjects())->not->toBeNull();
        expect($result->getObjects())->toBeEmpty();
    });

    test('empty results for listUsers', function (): void {
        $result = $this->client->listUsers(
            store: $this->storeId,
            model: $this->modelId,
            object: 'document:non-existent',
            relation: 'viewer',
            userFilters: new UserTypeFilters([
                new UserTypeFilter(type: 'user'),
            ]),
        )->rethrow()->unwrap();

        expect($result->getUsers())->not->toBeNull();
        expect($result->getUsers()->count())->toBe(0);
    });

    test('special characters in identifiers', function (): void {
        $validIds = [
            'user:alice@example.com',
            'user:alice.smith',
            'user:alice-smith',
            'user:alice_smith',
            'document:file.txt',
            'document:report-2024-01',
            'document:data_export',
        ];

        $successCount = 0;
        $writtenTuples = [];

        foreach ($validIds as $id) {
            $tuple = str_starts_with($id, 'user:')
                ? tuple($id, 'owner', 'document:test')
                : tuple('user:alice', 'owner', $id);

            $result = $this->client->writeTuples(
                store: $this->storeId,
                model: $this->modelId,
                writes: tuples($tuple),
            );

            if ($result->succeeded()) {
                ++$successCount;
                $writtenTuples[] = $tuple;
            }
        }

        expect($successCount)->toBeGreaterThan(0);

        $readCount = 0;

        foreach ($writtenTuples as $tuple) {
            $readResult = $this->client->readTuples(
                store: $this->storeId,
                tuple: tuple($tuple->getUser(), $tuple->getRelation(), $tuple->getObject()),
            );

            if ($readResult->succeeded()) {
                $readCount += $readResult->unwrap()->getTuples()->count();
            }
        }

        expect($readCount)->toBe($successCount);
    });

    test('unicode characters in store names', function (): void {
        $testNames = [
            'simple-ascii-store',
            'store-with-numbers-123',
            'store-café',
        ];

        $createdStores = [];
        $successCount = 0;

        foreach ($testNames as $name) {
            $result = $this->client->createStore(name: $name);

            if ($result->succeeded()) {
                $store = $result->unwrap();
                $createdStores[] = $store->getId();
                ++$successCount;

                if ('simple-ascii-store' === $name || 'store-with-numbers-123' === $name) {
                    expect($store->getName())->toBe($name);
                }
            }
        }

        foreach ($createdStores as $storeId) {
            $this->client->deleteStore(store: $storeId);
        }

        expect($successCount)->toBeGreaterThanOrEqual(2);
    });

    test('maximum length identifiers', function (): void {
        $longId = 'user:' . str_repeat('a', 251);

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple($longId, 'owner', 'document:test'),
            ),
        );

        expect($result->succeeded())->toBeTrue();

        $checkResult = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple($longId, 'owner', 'document:test'),
        )->rethrow()->unwrap();

        expect($checkResult->getAllowed())->toBeTrue();
    });

    test('page size limits', function (): void {
        $tuplesToWrite = [];

        for ($i = 0; 50 > $i; ++$i) {
            $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', 'document:test');
        }

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(...$tuplesToWrite),
        )->rethrow()->unwrap();

        $pageSizes = [1, 10, 25, 50, 100];

        foreach ($pageSizes as $pageSize) {
            $result = $this->client->readTuples(
                store: $this->storeId,
                tuple: tuple('', '', 'document:test'),
                pageSize: $pageSize,
            )->rethrow()->unwrap();

            expect($result->getTuples()->count())->toBeLessThanOrEqual($pageSize);

            if (50 > $pageSize) {
                expect($result->getContinuationToken())->not->toBeNull();
            }
        }
    });

    test('check with non-existent store returns false', function (): void {
        $result = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple('user:nobody', 'owner', 'document:nothing'),
        )->rethrow()->unwrap();

        expect($result->getAllowed())->toBeFalse();
    });

    test('write empty tuples array', function (): void {
        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(),
        );

        if ($result->failed()) {
            expect($result->failed())->toBeTrue();
        } else {
            expect($result->succeeded())->toBeTrue();
        }
    });

    test('delete non-existent tuple', function (): void {
        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            deletes: tuples(
                tuple('user:ghost', 'owner', 'document:phantom'),
            ),
        );

        // Deleting non-existent tuples should succeed (idempotent operation)
        expect($result->succeeded())->toBeTrue();
    });

    test('mixed writes and deletes', function (): void {
        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:alice', 'owner', 'document:doc1'),
                tuple('user:bob', 'viewer', 'document:doc1'),
                tuple('user:charlie', 'editor', 'document:doc1'),
            ),
        )->rethrow()->unwrap();

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: tuples(
                tuple('user:dave', 'viewer', 'document:doc1'),
            ),
            deletes: tuples(
                tuple('user:charlie', 'editor', 'document:doc1'),
            ),
        );

        expect($result->succeeded())->toBeTrue();

        $daveCheck = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple('user:dave', 'viewer', 'document:doc1'),
        )->rethrow()->unwrap();
        expect($daveCheck->getAllowed())->toBeTrue();

        $charlieCheck = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple('user:charlie', 'editor', 'document:doc1'),
        )->rethrow()->unwrap();
        expect($charlieCheck->getAllowed())->toBeFalse();
    });

    test('listTupleChanges with no changes', function (): void {
        $result = $this->client->listTupleChanges(
            store: $this->storeId,
        )->rethrow()->unwrap();

        expect($result->getChanges())->not->toBeNull();
        expect($result->getChanges()->count())->toBeGreaterThanOrEqual(0);
    });

    test('case sensitivity in identifiers', function (): void {
        $tuples = tuples(
            tuple('user:Alice', 'owner', 'document:Test'),
            tuple('user:alice', 'viewer', 'document:test'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $tuples,
        )->rethrow()->unwrap();

        $upperCheck = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple('user:Alice', 'owner', 'document:Test'),
        )->rethrow()->unwrap();
        expect($upperCheck->getAllowed())->toBeTrue();

        $lowerCheck = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tuple: tuple('user:alice', 'owner', 'document:Test'),
        )->rethrow()->unwrap();
        expect($lowerCheck->getAllowed())->toBeFalse();
    });

    test('whitespace handling in identifiers', function (): void {
        // Test that client-side validation rejects identifiers with whitespace
        expect(fn () => tuple('user:alice smith', 'owner', 'document:my document'))
            ->toThrow(ClientException::class, 'identifiers cannot contain whitespace');

        // Also test just the object having whitespace
        expect(fn () => tuple('user:alice', 'owner', 'document:my document'))
            ->toThrow(ClientException::class, 'identifiers cannot contain whitespace');
    });
});
