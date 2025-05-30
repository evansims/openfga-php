<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Models\Collections\UserTypeFilters;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\UserTypeFilter;

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
    $name = 'auth-query-test-' . bin2hex(random_bytes(5));
    $this->store = $this->client->createStore(name: $name)
        ->rethrow()
        ->unwrap();
    $this->storeId = $this->store->getId();

    // Create comprehensive authorization model for testing
    $dsl = '
        model
          schema 1.1

        type user

        type team
          relations
            define member: [user]
            define lead: [user]

        type organization
          relations
            define member: [user]
            define admin: [user]

        type folder
          relations
            define owner: [user]
            define editor: [user] or owner or editor from parent
            define viewer: [user] or editor or viewer from parent
            define parent: [folder]

        type document
          relations
            define owner: [user]
            define writer: [user] or owner or editor from parent
            define reader: [user, team#member] or writer or viewer from parent
            define parent: [folder]
    ';

    $model = $this->client->dsl($dsl)->rethrow()->unwrap();

    $createModelResponse = $this->client->createAuthorizationModel(
        store: $this->storeId,
        typeDefinitions: $model->getTypeDefinitions(),
    )->rethrow()->unwrap();

    $this->modelId = $createModelResponse->getModel();

    // Set up test data
    $tuplesToWrite = tuples(
        // Organization structure
        tuple('user:alice', 'admin', 'organization:acme'),
        tuple('user:bob', 'member', 'organization:acme'),

        // Team structure
        tuple('user:alice', 'lead', 'team:engineering'),
        tuple('user:bob', 'member', 'team:engineering'),
        tuple('user:charlie', 'member', 'team:engineering'),

        // Folder hierarchy
        tuple('user:alice', 'owner', 'folder:root'),
        tuple('folder:root', 'parent', 'folder:projects'),
        tuple('user:bob', 'editor', 'folder:projects'),
        tuple('folder:projects', 'parent', 'folder:project-alpha'),
        tuple('user:charlie', 'viewer', 'folder:project-alpha'),

        // Documents
        tuple('user:alice', 'owner', 'document:readme'),
        tuple('user:bob', 'writer', 'document:spec'),
        tuple('user:charlie', 'reader', 'document:notes'),
        tuple('folder:project-alpha', 'parent', 'document:design'),
        tuple('team:engineering#member', 'reader', 'document:handbook'),
    );

    $this->client->writeTuples(
        store: $this->storeId,
        model: $this->modelId,
        writes: $tuplesToWrite,
    )->rethrow()->unwrap();
});

afterEach(function (): void {
    // Clean up test store
    if (isset($this->storeId)) {
        $this->client->deleteStore(store: $this->storeId);
    }
});

test('performs basic authorization checks', function (): void {
    // Direct permission check
    $checkAliceOwnsReadme = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'owner', 'document:readme'),
    )->rethrow()->unwrap();

    expect($checkAliceOwnsReadme->getAllowed())->toBeTrue();

    // Check non-existent permission
    $checkBobOwnsReadme = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:bob', 'owner', 'document:readme'),
    )->rethrow()->unwrap();

    expect($checkBobOwnsReadme->getAllowed())->toBeFalse();

    // Check computed permission (owner implies writer)
    $checkAliceWritesReadme = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'writer', 'document:readme'),
    )->rethrow()->unwrap();

    expect($checkAliceWritesReadme->getAllowed())->toBeTrue();
});

test('performs inherited permission checks', function (): void {
    // Check folder inheritance - alice owns root, should have viewer access to project-alpha
    $checkAliceViewsProjectAlpha = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'viewer', 'folder:project-alpha'),
    )->rethrow()->unwrap();

    expect($checkAliceViewsProjectAlpha->getAllowed())->toBeTrue();

    // Check document inheritance - alice should be able to read design doc through folder hierarchy
    $checkAliceReadsDesign = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'reader', 'document:design'),
    )->rethrow()->unwrap();

    expect($checkAliceReadsDesign->getAllowed())->toBeTrue();
});

test('performs team-based permission checks', function (): void {
    // Check team membership permission
    $checkBobReadsHandbook = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:bob', 'reader', 'document:handbook'),
    )->rethrow()->unwrap();

    expect($checkBobReadsHandbook->getAllowed())->toBeTrue();

    // Charlie is also a team member
    $checkCharlieReadsHandbook = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:charlie', 'reader', 'document:handbook'),
    )->rethrow()->unwrap();

    expect($checkCharlieReadsHandbook->getAllowed())->toBeTrue();
});

test('expands permission relationships', function (): void {
    // Expand who can read the design document
    $expandResponse = $this->client->expand(
        store: $this->storeId,
        tupleKey: tuple('', 'reader', 'document:design'),
        model: $this->modelId,
    )->rethrow()->unwrap();

    $tree = $expandResponse->getTree();
    expect($tree)->not()->toBeNull();

    // The expansion should show the complex hierarchy
    expect($tree->getRoot())->not()->toBeNull();
});

test('lists objects user can access', function (): void {
    // List documents alice can read
    $objectsResponse = $this->client->listObjects(
        store: $this->storeId,
        model: $this->modelId,
        type: 'document',
        relation: 'reader',
        user: 'user:alice',
    )->rethrow()->unwrap();

    $objects = $objectsResponse->getObjects();
    expect($objects)->toContain('document:readme'); // Alice owns this
    expect($objects)->toContain('document:design'); // Alice has access through folder hierarchy

    // List documents bob can write
    $bobWriteResponse = $this->client->listObjects(
        store: $this->storeId,
        model: $this->modelId,
        type: 'document',
        relation: 'writer',
        user: 'user:bob',
    )->rethrow()->unwrap();

    $bobObjects = $bobWriteResponse->getObjects();
    expect($bobObjects)->toContain('document:spec'); // Bob is directly a writer
});

test('lists users with access to object', function (): void {
    // List users who can read the handbook
    $userFilter = new UserTypeFilter(type: 'user');
    $userFilters = new UserTypeFilters();
    $userFilters->add($userFilter);

    $usersResponse = $this->client->listUsers(
        store: $this->storeId,
        model: $this->modelId,
        object: 'document:handbook',
        relation: 'reader',
        userFilters: $userFilters,
    )->rethrow()->unwrap();

    $users = $usersResponse->getUsers();
    $userList = [];
    foreach ($users as $user) {
        $object = $user->getObject();
        $userList[] = \is_string($object) ? $object : (string) $object;
    }

    // All team members should have access
    expect($userList)->toContain('user:bob');
    expect($userList)->toContain('user:charlie');
});

test('handles contextual permissions with consistency', function (): void {
    // Test with eventual consistency
    $checkResponse = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'owner', 'document:readme'),
        consistency: Consistency::MINIMIZE_LATENCY,
    )->rethrow()->unwrap();

    expect($checkResponse->getAllowed())->toBeTrue();

    // Test with strong consistency
    $strongCheckResponse = $this->client->check(
        store: $this->storeId,
        model: $this->modelId,
        tupleKey: tuple('user:alice', 'owner', 'document:readme'),
        consistency: Consistency::HIGHER_CONSISTENCY,
    )->rethrow()->unwrap();

    expect($strongCheckResponse->getAllowed())->toBeTrue();
});
