<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\Enums\TupleOperation;

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
    $name = 'tuple-changes-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create authorization model
    $dsl = '
        model
          schema 1.1

        type user

        type group
          relations
            define member: [user]

        type document
          relations
            define owner: [user]
            define editor: [user, group#member] or owner
            define viewer: [user, group#member] or editor
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

test('list changes after writes', function (): void {
    // Write some tuples
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:bob', 'viewer', 'document:doc1'),
            tuple('user:charlie', 'editor', 'document:doc2'),
        ),
    )->rethrow()->unwrap();

    // List changes
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    expect($result->getChanges())->not->toBeNull();
    expect($result->getChanges()->count())->toBeGreaterThanOrEqual(3);

    // Check the changes
    $changes = [];
    foreach ($result->getChanges() as $change) {
        $changes[] = $change;

        // Verify change structure
        expect($change->getTimestamp())->toBeInstanceOf(DateTimeInterface::class);
        expect($change->getTupleKey())->not->toBeNull();
        expect($change->getOperation())->toBeIn([
            TupleOperation::TUPLE_OPERATION_WRITE,
            TupleOperation::TUPLE_OPERATION_DELETE,
        ]);
    }

    // Should have our writes
    expect(\count($changes))->toBeGreaterThanOrEqual(3);
});

test('list changes after deletes', function (): void {
    // First write tuples
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:bob', 'viewer', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // Then delete one
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        deletes: tuples(
            tuple('user:bob', 'viewer', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // List all changes
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    $deleteFound = false;
    foreach ($result->getChanges() as $change) {
        if (TupleOperation::TUPLE_OPERATION_DELETE === $change->getOperation()) {
            $deleteFound = true;
            $tupleKey = $change->getTupleKey();
            expect($tupleKey->getUser())->toBe('user:bob');
            expect($tupleKey->getRelation())->toBe('viewer');
            expect($tupleKey->getObject())->toBe('document:doc1');
        }
    }

    expect($deleteFound)->toBeTrue();
});

test('list changes with pagination', function (): void {
    // Write many tuples to ensure pagination
    $tuplesToWrite = [];
    for ($i = 0; $i < 20; ++$i) {
        $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', "document:doc{$i}");
    }

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(...$tuplesToWrite),
    )->rethrow()->unwrap();

    // Get first page
    $firstPage = $this->client->listTupleChanges(
        store: $this->storeId,
        pageSize: 5,
    )->rethrow()->unwrap();

    expect($firstPage->getChanges()->count())->toBeLessThanOrEqual(5);

    $continuationToken = $firstPage->getContinuationToken();
    if ($continuationToken) {
        // Get second page
        $secondPage = $this->client->listTupleChanges(
            store: $this->storeId,
            pageSize: 5,
            continuationToken: $continuationToken,
        )->rethrow()->unwrap();

        expect($secondPage->getChanges())->not->toBeNull();

        // Timestamps should be different or in order
        if ($firstPage->getChanges()->count() > 0 && $secondPage->getChanges()->count() > 0) {
            // Get the changes as arrays
            $firstPageChanges = [];
            foreach ($firstPage->getChanges() as $change) {
                $firstPageChanges[] = $change;
            }
            $secondPageChanges = [];
            foreach ($secondPage->getChanges() as $change) {
                $secondPageChanges[] = $change;
            }

            $firstPageLast = end($firstPageChanges);
            $secondPageFirst = reset($secondPageChanges);

            // Changes should be in chronological order
            expect($secondPageFirst->getTimestamp()->getTimestamp())
                ->toBeGreaterThanOrEqual($firstPageLast->getTimestamp()->getTimestamp());
        }
    }
});

test('list changes filtered by type', function (): void {
    // Write tuples for different types
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:bob', 'member', 'group:engineering'),
            tuple('user:charlie', 'viewer', 'document:doc2'),
        ),
    )->rethrow()->unwrap();

    // List changes filtered by document type
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
        type: 'document',
    )->rethrow()->unwrap();

    // Should only have document changes
    foreach ($result->getChanges() as $change) {
        $object = $change->getTupleKey()->getObject();
        expect($object)->toStartWith('document:');
    }
});

test('list changes chronological order', function (): void {
    // Write tuples with delays to ensure different timestamps
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:first'),
        ),
    )->rethrow()->unwrap();

    // Small delay
    usleep(100000); // 100ms

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:bob', 'owner', 'document:second'),
        ),
    )->rethrow()->unwrap();

    // List all changes
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    $timestamps = [];
    foreach ($result->getChanges() as $change) {
        $timestamps[] = $change->getTimestamp()->getTimestamp();
    }

    // Should be in chronological order (oldest first)
    $sortedTimestamps = $timestamps;
    sort($sortedTimestamps);
    expect($timestamps)->toBe($sortedTimestamps);
});

test('list changes empty store', function (): void {
    // Create a new store just for this test
    $emptyStore = $this->client->createStore(
        name: 'empty-changes-test-' . bin2hex(random_bytes(5)),
    )->rethrow()->unwrap();

    try {
        $result = $this->client->listTupleChanges(
            store: $emptyStore->getId(),
        )->rethrow()->unwrap();

        expect($result->getChanges())->not->toBeNull();
        // Might have some initial changes from store creation
        expect($result->getChanges()->count())->toBeGreaterThanOrEqual(0);
    } finally {
        $this->client->deleteStore(store: $emptyStore->getId());
    }
});

test('list changes with mixed operations', function (): void {
    // Initial writes
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:doc1'),
            tuple('user:bob', 'viewer', 'document:doc1'),
            tuple('user:charlie', 'editor', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // Mixed write and delete
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:dave', 'viewer', 'document:doc1'),
        ),
        deletes: tuples(
            tuple('user:charlie', 'editor', 'document:doc1'),
        ),
    )->rethrow()->unwrap();

    // List all changes
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    $operations = [];
    foreach ($result->getChanges() as $change) {
        $operations[] = $change->getOperation();
    }

    // Should have both write and delete operations
    expect($operations)->toContain(TupleOperation::TUPLE_OPERATION_WRITE);
    expect($operations)->toContain(TupleOperation::TUPLE_OPERATION_DELETE);
});

test('tuple change metadata', function (): void {
    // Write a tuple
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:important'),
        ),
    )->rethrow()->unwrap();

    // Get changes
    $result = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    // Find our change
    $found = false;
    foreach ($result->getChanges() as $change) {
        $tupleKey = $change->getTupleKey();
        if ('user:alice' === $tupleKey->getUser()
            && 'document:important' === $tupleKey->getObject()) {
            $found = true;

            // Verify all metadata is present
            expect($change->getTimestamp())->toBeInstanceOf(DateTimeInterface::class);
            expect($change->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_WRITE);
            expect($tupleKey->getUser())->toBe('user:alice');
            expect($tupleKey->getRelation())->toBe('owner');
            expect($tupleKey->getObject())->toBe('document:important');
        }
    }

    expect($found)->toBeTrue();
});

test('continuation token persistence', function (): void {
    // Write many tuples
    $tuplesToWrite = [];
    for ($i = 0; $i < 15; ++$i) {
        $tuplesToWrite[] = tuple("user:user{$i}", 'viewer', "document:doc{$i}");
    }

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(...$tuplesToWrite),
    )->rethrow()->unwrap();

    // Get first page with small size
    $firstResult = $this->client->listTupleChanges(
        store: $this->storeId,
        pageSize: 3,
    )->rethrow()->unwrap();

    $token1 = $firstResult->getContinuationToken();
    expect($token1)->not->toBeNull();

    // Use token to get second page
    $secondResult = $this->client->listTupleChanges(
        store: $this->storeId,
        pageSize: 3,
        continuationToken: $token1,
    )->rethrow()->unwrap();

    $token2 = $secondResult->getContinuationToken();

    // Use same token again - should get same results
    $secondResultAgain = $this->client->listTupleChanges(
        store: $this->storeId,
        pageSize: 3,
        continuationToken: $token1,
    )->rethrow()->unwrap();

    // Should get same number of results
    expect($secondResultAgain->getChanges()->count())
        ->toBe($secondResult->getChanges()->count());
});
