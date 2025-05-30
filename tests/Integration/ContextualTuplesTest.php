<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\Enums\Consistency;

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
    $name = 'contextual-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create authorization model without conditions (DSL doesn't support them yet)
    $dsl = '
        model
          schema 1.1

        type user

        type organization
          relations
            define member: [user]
            define admin: [user]

        type document
          relations
            define owner: [user]
            define editor: [user, organization#member] or owner
            define viewer: [user, organization#member] or editor
            define can_share: [user]
            define restricted_viewer: [user]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $this->storeId,
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $this->modelId = $createModelResponse->getModel();

    // Set up base tuples
    $baseTuples = tuples(
        // Organization structure
        tuple('user:alice', 'member', 'organization:acme'),
        tuple('user:bob', 'member', 'organization:acme'),

        // Document ownership
        tuple('user:alice', 'owner', 'document:strategy'),
        tuple('organization:acme#member', 'viewer', 'document:public-doc'),

        // Conditional relations
        tuple('user:charlie', 'admin', 'organization:acme'),
        tuple('user:dave', 'can_share', 'document:premium-content'),
        tuple('user:eve', 'restricted_viewer', 'document:confidential'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $baseTuples,
    )->rethrow()->unwrap();
});

afterEach(function (): void {
    // Clean up test store
    if (isset($this->storeId)) {
        $this->client->deleteStore(store: $this->storeId);
    }
});

test('check with contextual tuples', function (): void {
    // Check without contextual tuples - Frank should NOT have access
    $checkWithoutContext = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:frank', 'viewer', 'document:public-doc'),
    )->rethrow()->unwrap();

    expect($checkWithoutContext->getAllowed())->toBeFalse();

    // Check with contextual tuples - Frank should have access through org membership
    $contextualTuples = tuples(
        tuple('user:frank', 'member', 'organization:acme'),
    );

    $checkWithContext = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:frank', 'viewer', 'document:public-doc'),
        contextualTuples: $contextualTuples,
    )->rethrow()->unwrap();

    expect($checkWithContext->getAllowed())->toBeTrue();
});

test('list objects with contextual tuples', function (): void {
    // List objects Frank can view without context
    $withoutContext = $this->client->listObjects(
        store: $this->storeId,
        model: $this->modelId,
        type: 'document',
        relation: 'viewer',
        user: 'user:frank',
    )->rethrow()->unwrap();

    expect($withoutContext->getObjects())->toHaveCount(0);

    // List objects Frank can view with contextual org membership
    $contextualTuples = tuples(
        tuple('user:frank', 'member', 'organization:acme'),
    );

    $withContext = $this->client->listObjects(
        store: $this->storeId,
        model: $this->modelId,
        type: 'document',
        relation: 'viewer',
        user: 'user:frank',
        contextualTuples: $contextualTuples,
    )->rethrow()->unwrap();

    expect($withContext->getObjects())->toContain('document:public-doc');
});

test('list users with contextual tuples', function (): void {
    // Create user filters
    $userFilter = new OpenFGA\Models\UserTypeFilter(type: 'user');
    $userFilters = new OpenFGA\Models\Collections\UserTypeFilters();
    $userFilters->add($userFilter);

    // List users who can view public-doc without extra context
    $withoutContextResult = $this->client->listUsers(
        store: $this->storeId,
        model: $this->modelId,
        object: 'document:public-doc',
        relation: 'viewer',
        userFilters: $userFilters,
    );

    $withoutContext = $withoutContextResult->rethrow()->unwrap();

    $baseUsers = [];
    foreach ($withoutContext->getUsers() as $user) {
        $object = $user->getObject();
        $baseUsers[] = \is_string($object) ? $object : (string) $object;
    }

    // Should include Alice and Bob (org members)
    expect($baseUsers)->toContain('user:alice');
    expect($baseUsers)->toContain('user:bob');
    expect($baseUsers)->not->toContain('user:frank');

    // List users with Frank as contextual member
    $contextualTuples = tuples(
        tuple('user:frank', 'member', 'organization:acme'),
    );

    $withContext = $this->client->listUsers(
        store: $this->storeId,
        model: $this->modelId,
        object: 'document:public-doc',
        relation: 'viewer',
        userFilters: $userFilters,
        contextualTuples: $contextualTuples,
    )->rethrow()->unwrap();

    $contextUsers = [];
    foreach ($withContext->getUsers() as $user) {
        $object = $user->getObject();
        $contextUsers[] = \is_string($object) ? $object : (string) $object;
    }

    // Should now include Frank
    expect($contextUsers)->toContain('user:frank');
});

test('expand with contextual tuples', function (): void {
    // Expand viewer relationship on public-doc
    $expandResponse = $this->client->expand(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('', 'viewer', 'document:public-doc'),
    )->rethrow()->unwrap();

    $tree = $expandResponse->getTree();
    expect($tree)->not()->toBeNull();

    // TODO: Add more specific assertions about the expansion tree
    // once we understand the tree structure better
});

test('multiple contextual tuples', function (): void {
    // Grace needs multiple contextual relationships
    $contextualTuples = tuples(
        tuple('user:grace', 'member', 'organization:acme'),
        tuple('user:grace', 'owner', 'document:temp-doc'),
    );

    // Check if Grace can view through contextual membership
    $checkView = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:grace', 'viewer', 'document:public-doc'),
        contextualTuples: $contextualTuples,
    )->rethrow()->unwrap();

    expect($checkView->getAllowed())->toBeTrue();

    // Check if Grace can edit her contextual owned doc
    $checkEdit = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:grace', 'editor', 'document:temp-doc'),
        contextualTuples: $contextualTuples,
    )->rethrow()->unwrap();

    expect($checkEdit->getAllowed())->toBeTrue();
});

test('consistency modes with contextual data', function (): void {
    $contextualTuples = tuples(
        tuple('user:henry', 'member', 'organization:acme'),
    );

    // Test with minimize latency
    $checkMinLatency = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:henry', 'viewer', 'document:public-doc'),
        contextualTuples: $contextualTuples,
        consistency: Consistency::MINIMIZE_LATENCY,
    )->rethrow()->unwrap();

    expect($checkMinLatency->getAllowed())->toBeTrue();

    // Test with higher consistency
    $checkHighConsistency = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:henry', 'viewer', 'document:public-doc'),
        contextualTuples: $contextualTuples,
        consistency: Consistency::HIGHER_CONSISTENCY,
    )->rethrow()->unwrap();

    expect($checkHighConsistency->getAllowed())->toBeTrue();
});
