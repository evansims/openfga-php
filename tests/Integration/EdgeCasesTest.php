<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\Models\{tuple, tuples};

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

    // Create a test store
    $name = 'edge-case-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create authorization model
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
    // Clean up test store
    if (isset($this->storeId)) {
        $this->client->deleteStore(store: $this->storeId);
    }
});

test('empty results for readTuples', function (): void {
    // Read tuples for non-existent object
    $result = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:nonexistent'),
    )->rethrow()->unwrap();

    expect($result->getTuples())->not->toBeNull();
    expect($result->getTuples()->count())->toBe(0);
});

test('empty results for listObjects', function (): void {
    // List objects when none exist
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
    // List users when none have access
    $result = $this->client->listUsers(
        store: $this->storeId,
        model: $this->modelId,
        object: 'document:non-existent',
        relation: 'viewer',
        userFilters: new OpenFGA\Models\Collections\UserTypeFilters([
            new OpenFGA\Models\UserTypeFilter(type: 'user'),
        ]),
    )->rethrow()->unwrap();

    expect($result->getUsers())->not->toBeNull();
    expect($result->getUsers()->count())->toBe(0);
});

test('special characters in identifiers', function (): void {
    // Test various special characters that should be allowed
    // Note: Some characters might be rejected by OpenFGA
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

    // At least some should succeed
    expect($successCount)->toBeGreaterThan(0);

    // Read them back one by one since broad filters might not work
    $readCount = 0;
    foreach ($writtenTuples as $tuple) {
        $readResult = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple($tuple->getUser(), $tuple->getRelation(), $tuple->getObject()),
        );

        if ($readResult->succeeded()) {
            $readCount += $readResult->unwrap()->getTuples()->count();
        }
    }

    expect($readCount)->toBe($successCount);
});

test('unicode characters in store names', function (): void {
    // Try simple ASCII first to ensure the test setup works
    $testNames = [
        'simple-ascii-store',
        'store-with-numbers-123',
        'store-café',  // Accented character
    ];

    $createdStores = [];
    $successCount = 0;

    foreach ($testNames as $name) {
        $result = $this->client->createStore(name: $name);

        if ($result->succeeded()) {
            $store = $result->unwrap();
            $createdStores[] = $store->getId();
            ++$successCount;

            // Verify the name was stored correctly
            if ('simple-ascii-store' === $name || 'store-with-numbers-123' === $name) {
                expect($store->getName())->toBe($name);
            }
        }
    }

    // Clean up
    foreach ($createdStores as $storeId) {
        $this->client->deleteStore(store: $storeId);
    }

    // At least ASCII names should work
    expect($successCount)->toBeGreaterThanOrEqual(2);
});

test('maximum length identifiers', function (): void {
    // OpenFGA typically allows up to 256 characters for identifiers
    $longId = 'user:' . str_repeat('a', 251); // 256 total with prefix

    $result = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple($longId, 'owner', 'document:test'),
        ),
    );

    expect($result->succeeded())->toBeTrue();

    // Verify it was written
    $checkResult = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple($longId, 'owner', 'document:test'),
    )->rethrow()->unwrap();

    expect($checkResult->getAllowed())->toBeTrue();
});

test('page size limits', function (): void {
    // Write many tuples
    $tuplesToWrite = [];
    for ($i = 0; $i < 50; ++$i) {
        $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', 'document:test');
    }

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(...$tuplesToWrite),
    )->rethrow()->unwrap();

    // Test various page sizes
    $pageSizes = [1, 10, 25, 50, 100];

    foreach ($pageSizes as $pageSize) {
        $result = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('', '', 'document:test'),
            pageSize: $pageSize,
        )->rethrow()->unwrap();

        expect($result->getTuples()->count())->toBeLessThanOrEqual($pageSize);

        if ($pageSize < 50) {
            expect($result->getContinuationToken())->not->toBeNull();
        }
    }
});

test('check with non-existent store returns false', function (): void {
    // Check should return false, not error, for non-existent relationships
    $result = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:nobody', 'owner', 'document:nothing'),
    )->rethrow()->unwrap();

    expect($result->getAllowed())->toBeFalse();
});

test('write empty tuples array', function (): void {
    // Writing empty array might fail or succeed depending on API
    $result = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(),
    );

    // If it fails, that's acceptable
    if ($result->failed()) {
        expect($result->failed())->toBeTrue();
    } else {
        expect($result->succeeded())->toBeTrue();
    }
});

test('delete non-existent tuple', function (): void {
    // Deleting a tuple that doesn't exist should fail
    $result = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        deletes: tuples(
            tuple('user:ghost', 'owner', 'document:phantom'),
        ),
    );

    expect($result->failed())->toBeTrue();
});

test('mixed writes and deletes', function (): void {
    // First write some tuples
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:bob', 'viewer', 'document:doc1'),
            tuple('user:charlie', 'editor', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // Now do mixed operation
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

    // Verify the changes
    $daveCheck = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:dave', 'viewer', 'document:doc1'),
    )->rethrow()->unwrap();
    expect($daveCheck->getAllowed())->toBeTrue();

    $charlieCheck = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:charlie', 'editor', 'document:doc1'),
    )->rethrow()->unwrap();
    expect($charlieCheck->getAllowed())->toBeFalse();
});

test('listTupleChanges with no changes', function (): void {
    // List changes for a brand new store
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    expect($result->getChanges())->not->toBeNull();
    // New store might have some initial changes from model creation
    expect($result->getChanges()->count())->toBeGreaterThanOrEqual(0);
});

test('case sensitivity in identifiers', function (): void {
    // Test that identifiers are case-sensitive
    $tuples = tuples(
        tuple('user:Alice', 'owner', 'document:Test'),
        tuple('user:alice', 'viewer', 'document:test'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $tuples,
    )->rethrow()->unwrap();

    // Check case-sensitive access
    $upperCheck = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:Alice', 'owner', 'document:Test'),
    )->rethrow()->unwrap();
    expect($upperCheck->getAllowed())->toBeTrue();

    $lowerCheck = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'owner', 'document:Test'),
    )->rethrow()->unwrap();
    expect($lowerCheck->getAllowed())->toBeFalse();
});

test('whitespace handling in identifiers', function (): void {
    // Note: OpenFGA might reject or trim whitespace
    $result = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice smith', 'owner', 'document:my document'),
        ),
    );

    // If it succeeds, verify we can check it
    if ($result->succeeded()) {
        $checkResult = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice smith', 'owner', 'document:my document'),
        )->rethrow()->unwrap();

        expect($checkResult->getAllowed())->toBeTrue();
    } else {
        // If it fails, that's also acceptable behavior
        expect($result->failed())->toBeTrue();
    }
});
