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

    // Create a test store and authorization model
    $name = 'tuple-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create authorization model using DSL
    $dsl = <<<'DSL'
        model
          schema 1.1
        type user
        type organization
          relations
            define member: [user]
            define admin: [user]
        type document
          relations
            define reader: [user]
            define writer: [user]
            define owner: [user]
        DSL;

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

test('writes and reads relationship tuples', function (): void {
    $tuplesToWrite = tuples(
        tuple('user:alice', 'owner', 'document:readme'),
        tuple('user:bob', 'reader', 'document:readme'),
        tuple('user:charlie', 'writer', 'document:spec'),
    );

    // Write tuples
    $writeResponse = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();

    expect($writeResponse)->not()->toBeNull();

    // Read tuples back - we need to read with specific filters since OpenFGA doesn't support reading all tuples
    // Read tuples for document:readme with strong consistency
    $readResponse1 = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:readme'),
        consistency: OpenFGA\Models\Enums\Consistency::HIGHER_CONSISTENCY,
    )->rethrow()->unwrap();

    expect($readResponse1->getTuples()->count())->toBe(2); // alice as owner, bob as reader

    // Read tuples for document:spec
    $readResponse2 = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:spec'),
    )->rethrow()->unwrap();

    expect($readResponse2->getTuples()->count())->toBe(1); // charlie as writer

    // Verify specific tuples exist for document:readme
    $tupleStrings1 = [];
    foreach ($readResponse1->getTuples() as $tuple) {
        $key = $tuple->getKey();
        $tupleStrings1[] = \sprintf(
            '%s#%s@%s',
            $key->getObject(),
            $key->getRelation(),
            $key->getUser(),
        );
    }

    expect($tupleStrings1)->toContain('document:readme#owner@user:alice');
    expect($tupleStrings1)->toContain('document:readme#reader@user:bob');

    // Verify specific tuples exist for document:spec
    $tupleStrings2 = [];
    foreach ($readResponse2->getTuples() as $tuple) {
        $key = $tuple->getKey();
        $tupleStrings2[] = \sprintf(
            '%s#%s@%s',
            $key->getObject(),
            $key->getRelation(),
            $key->getUser(),
        );
    }

    expect($tupleStrings2)->toContain('document:spec#writer@user:charlie');
});

test('deletes relationship tuples', function (): void {
    // First write some tuples
    $tuplesToWrite = tuples(
        tuple('user:alice', 'owner', 'document:test'),
        tuple('user:bob', 'reader', 'document:test'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();

    // Verify tuples exist - read for specific object
    $readResponse = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:test'),
    )->rethrow()->unwrap();

    expect($readResponse->getTuples()->count())->toBe(2);

    // Delete one tuple
    $deleteResponse = $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        deletes: tuples(tuple('user:bob', 'reader', 'document:test')),
    )->rethrow()->unwrap();

    expect($deleteResponse)->not()->toBeNull();

    // Verify only one tuple remains
    $readAfterDelete = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:test'),
    )->rethrow()->unwrap();

    expect($readAfterDelete->getTuples()->count())->toBe(1);

    $remainingTuple = $readAfterDelete->getTuples()->first();
    $key = $remainingTuple->getKey();
    expect($key->getUser())->toBe('user:alice');
    expect($key->getRelation())->toBe('owner');
    expect($key->getObject())->toBe('document:test');
});

test('reads tuples with filters', function (): void {
    // Write tuples for multiple objects and users
    $tuplesToWrite = tuples(
        tuple('user:alice', 'owner', 'document:doc1'),
        tuple('user:alice', 'reader', 'document:doc2'),
        tuple('user:bob', 'reader', 'document:doc1'),
        tuple('user:bob', 'writer', 'document:doc2'),
        tuple('user:charlie', 'reader', 'document:doc3'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();

    // Filter by object - this is supported
    $readByObject = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', '', 'document:doc1'),
    )->rethrow()->unwrap();

    expect($readByObject->getTuples()->count())->toBe(2); // alice as owner, bob as reader

    // Filter by user and object - this is supported
    $readByUserAndObject = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('user:alice', '', 'document:doc1'),
    )->rethrow()->unwrap();

    expect($readByUserAndObject->getTuples()->count())->toBe(1); // alice as owner

    // Filter by relation and object - this is supported
    $readByRelationAndObject = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('', 'reader', 'document:doc1'),
    )->rethrow()->unwrap();

    expect($readByRelationAndObject->getTuples()->count())->toBe(1); // bob as reader

    // Verify multiple filters work together
    $readByAllFilters = $this->client->readTuples(
        store: $this->storeId,
        tupleKey: tuple('user:bob', 'writer', 'document:doc2'),
    )->rethrow()->unwrap();

    expect($readByAllFilters->getTuples()->count())->toBe(1); // exact match
});

test('handles tuple changes over time', function (): void {
    // Write initial tuples
    $initialTuples = tuples(
        tuple('user:alice', 'owner', 'document:timeline'),
        tuple('user:bob', 'reader', 'document:timeline'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $initialTuples,
    )->rethrow()->unwrap();

    // List tuple changes
    $changesResponse = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    $changes = $changesResponse->getChanges();
    expect($changes->count())->toBeGreaterThanOrEqual(2);

    // Verify we have write operations
    $operations = [];
    foreach ($changes as $change) {
        $operations[] = $change->getOperation()->value;
    }

    expect($operations)->toContain('TUPLE_OPERATION_WRITE');

    // Add more tuples and check changes again
    $moreTuples = tuples(
        tuple('user:charlie', 'writer', 'document:timeline'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $moreTuples,
    )->rethrow()->unwrap();

    $updatedChanges = $this->client->listTupleChanges(
        store: $this->storeId,
    )->rethrow()->unwrap();

    expect($updatedChanges->getChanges()->count())->toBeGreaterThan($changes->count());
});
