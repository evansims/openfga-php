<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;

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
    $name = 'assertions-test-' . bin2hex(random_bytes(5));
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

test('write and read assertions', function (): void {
    // Create assertions
    $assertions = new Assertions([
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'owner', 'document:readme'),
            expectation: true,
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'viewer', 'document:readme'),
            expectation: true,
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:charlie', 'editor', 'document:readme'),
            expectation: false,
        ),
    ]);

    // Write assertions
    $writeResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $assertions,
    );

    expect($writeResult->succeeded())->toBeTrue();

    // Read assertions back
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions())->not->toBeNull();
    expect($readResult->getAssertions()->count())->toBe(3);
    expect($readResult->getModel())->toBe($this->modelId);

    // Verify assertions content
    $readAssertions = [];
    foreach ($readResult->getAssertions() as $assertion) {
        $key = $assertion->getTupleKey();
        $readAssertions[] = [
            'user' => $key->getUser(),
            'relation' => $key->getRelation(),
            'object' => $key->getObject(),
            'expectation' => $assertion->getExpectation(),
        ];
    }

    expect($readAssertions)->toContain([
        'user' => 'user:alice',
        'relation' => 'owner',
        'object' => 'document:readme',
        'expectation' => true,
    ]);

    expect($readAssertions)->toContain([
        'user' => 'user:bob',
        'relation' => 'viewer',
        'object' => 'document:readme',
        'expectation' => true,
    ]);

    expect($readAssertions)->toContain([
        'user' => 'user:charlie',
        'relation' => 'editor',
        'object' => 'document:readme',
        'expectation' => false,
    ]);
});

test('update existing assertions', function (): void {
    // Write initial assertions
    $initialAssertions = new Assertions([
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'owner', 'document:test'),
            expectation: true,
        ),
    ]);

    $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $initialAssertions,
    )->rethrow()->unwrap();

    // Update with different expectations
    $updatedAssertions = new Assertions([
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'owner', 'document:test'),
            expectation: false, // Changed from true to false
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'viewer', 'document:test'),
            expectation: true, // New assertion
        ),
    ]);

    $updateResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $updatedAssertions,
    );

    expect($updateResult->succeeded())->toBeTrue();

    // Read and verify
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions()->count())->toBe(2);
});

test('assertions with contextual tuples', function (): void {
    // Test that assertions support contextual tuples
    // The expectation is that with the contextual tuple, alice should be able to view the document

    // Create assertions with contextual tuples
    $assertions = new Assertions([
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'viewer', 'document:contextual'),
            expectation: true,
            contextualTuples: tuples(
                tuple('user:alice', 'editor', 'document:contextual'),
            ),
        ),
        // Also test without contextual tuples for comparison
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'viewer', 'document:contextual'),
            expectation: false, // Bob has no access without contextual tuples
        ),
        // Test with context parameter
        new Assertion(
            tupleKey: new AssertionTupleKey('user:charlie', 'editor', 'document:contextual'),
            expectation: true,
            context: ['environment' => 'production'],
        ),
    ]);

    $writeResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $assertions,
    );

    expect($writeResult->succeeded())->toBeTrue();

    // Read assertions back
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions())->not->toBeNull();
    expect($readResult->getAssertions()->count())->toBe(3);

    // Verify the assertions were stored correctly
    $foundAssertions = [];
    foreach ($readResult->getAssertions() as $assertion) {
        $key = $assertion->getTupleKey();
        $contextualTuples = $assertion->getContextualTuples();
        $foundAssertions[$key->getUser()] = [
            'relation' => $key->getRelation(),
            'object' => $key->getObject(),
            'expectation' => $assertion->getExpectation(),
            'has_contextual_tuples' => null !== $contextualTuples && $contextualTuples->count() > 0,
            'has_context' => null !== $assertion->getContext(),
        ];
    }

    // Verify alice's assertion with contextual tuples
    expect($foundAssertions)->toHaveKey('user:alice');
    expect($foundAssertions['user:alice']['expectation'])->toBeTrue();
    expect($foundAssertions['user:alice']['has_contextual_tuples'])->toBeTrue();

    // Verify bob's assertion without contextual tuples
    expect($foundAssertions)->toHaveKey('user:bob');
    expect($foundAssertions['user:bob']['expectation'])->toBeFalse();
    expect($foundAssertions['user:bob']['has_contextual_tuples'])->toBeFalse();

    // Verify charlie's assertion with context
    expect($foundAssertions)->toHaveKey('user:charlie');
    expect($foundAssertions['user:charlie']['expectation'])->toBeTrue();
    expect($foundAssertions['user:charlie']['has_context'])->toBeTrue();

    // Verify contextual tuples content for alice
    foreach ($readResult->getAssertions() as $assertion) {
        if ('user:alice' === $assertion->getTupleKey()->getUser()) {
            $contextualTuples = $assertion->getContextualTuples();
            expect($contextualTuples)->not->toBeNull();
            expect($contextualTuples->count())->toBe(1);

            $contextualTuple = $contextualTuples->first();
            expect($contextualTuple->getUser())->toBe('user:alice');
            expect($contextualTuple->getRelation())->toBe('editor');
            expect($contextualTuple->getObject())->toBe('document:contextual');
        }
    }
});

test('empty assertions list', function (): void {
    // Write empty assertions
    $emptyAssertions = new Assertions([]);

    $writeResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $emptyAssertions,
    );

    expect($writeResult->succeeded())->toBeTrue();

    // Read should return empty list
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions())->not->toBeNull();
    expect($readResult->getAssertions()->count())->toBe(0);
});

test('assertions with all relation types', function (): void {
    // Test assertions for different relation types
    $assertions = new Assertions([
        // Direct relation
        new Assertion(
            tupleKey: new AssertionTupleKey('user:owner', 'owner', 'document:direct'),
            expectation: true,
        ),
        // Computed relation through OR
        new Assertion(
            tupleKey: new AssertionTupleKey('user:editor', 'editor', 'document:computed'),
            expectation: true,
        ),
        // Transitive relation
        new Assertion(
            tupleKey: new AssertionTupleKey('user:viewer', 'viewer', 'document:transitive'),
            expectation: true,
        ),
    ]);

    $writeResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $assertions,
    );

    expect($writeResult->succeeded())->toBeTrue();

    // Verify all were written
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions()->count())->toBe(3);
});

test('assertions validation against model', function (): void {
    // Write some tuples that make assertions true
    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:important'),
        ),
    )->rethrow()->unwrap();

    // Create assertions that should pass validation
    $assertions = new Assertions([
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'owner', 'document:important'),
            expectation: true, // Should be true based on tuple
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:alice', 'viewer', 'document:important'),
            expectation: true, // Should be true through inheritance
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'viewer', 'document:important'),
            expectation: false, // Should be false - no access
        ),
    ]);

    $writeResult = $this->client->writeAssertions(
        store: $this->storeId,
        model: $this->modelId,
        assertions: $assertions,
    );

    expect($writeResult->succeeded())->toBeTrue();

    // Verify they can be read back
    $readResult = $this->client->readAssertions(
        store: $this->storeId,
        model: $this->modelId,
    )->rethrow()->unwrap();

    expect($readResult->getAssertions()->count())->toBe(3);
});
