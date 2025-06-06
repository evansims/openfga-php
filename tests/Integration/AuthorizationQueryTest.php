<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Models\Collections\UserTypeFilters;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\UserTypeFilter;

use function is_string;
use function OpenFGA\{tuple, tuples};

describe('Authorization Queries', function (): void {
    beforeEach(function (): void {
        $this->responseFactory = new Psr17Factory;
        $this->httpClient = new FileGetContents($this->responseFactory);
        $this->httpRequestFactory = $this->responseFactory;
        $this->httpStreamFactory = $this->responseFactory;
        $this->url = getOpenFgaUrl();

        $this->client = new Client(
            url: $this->url,
            httpClient: $this->httpClient,
            httpResponseFactory: $this->responseFactory,
            httpStreamFactory: $this->httpStreamFactory,
            httpRequestFactory: $this->httpRequestFactory,
        );

        $name = 'auth-query-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();
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

        $tuplesToWrite = tuples(
            tuple('user:alice', 'admin', 'organization:acme'),
            tuple('user:bob', 'member', 'organization:acme'),
            tuple('user:alice', 'lead', 'team:engineering'),
            tuple('user:bob', 'member', 'team:engineering'),
            tuple('user:charlie', 'member', 'team:engineering'),
            tuple('user:alice', 'owner', 'folder:root'),
            tuple('folder:root', 'parent', 'folder:projects'),
            tuple('user:bob', 'editor', 'folder:projects'),
            tuple('folder:projects', 'parent', 'folder:project-alpha'),
            tuple('user:charlie', 'viewer', 'folder:project-alpha'),
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
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('checks authorization', function (): void {
        $checkAliceOwnsReadme = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'owner', 'document:readme'),
        )->rethrow()->unwrap();

        expect($checkAliceOwnsReadme->getAllowed())->toBeTrue();

        $checkBobOwnsReadme = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:bob', 'owner', 'document:readme'),
        )->rethrow()->unwrap();

        expect($checkBobOwnsReadme->getAllowed())->toBeFalse();

        $checkAliceWritesReadme = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'writer', 'document:readme'),
        )->rethrow()->unwrap();

        expect($checkAliceWritesReadme->getAllowed())->toBeTrue();
    });

    test('checks inherited permissions', function (): void {
        $checkAliceViewsProjectAlpha = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'viewer', 'folder:project-alpha'),
        )->rethrow()->unwrap();

        expect($checkAliceViewsProjectAlpha->getAllowed())->toBeTrue();

        $checkAliceReadsDesign = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'reader', 'document:design'),
        )->rethrow()->unwrap();

        expect($checkAliceReadsDesign->getAllowed())->toBeTrue();
    });

    test('checks team permissions', function (): void {
        $checkBobReadsHandbook = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:bob', 'reader', 'document:handbook'),
        )->rethrow()->unwrap();

        expect($checkBobReadsHandbook->getAllowed())->toBeTrue();

        $checkCharlieReadsHandbook = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:charlie', 'reader', 'document:handbook'),
        )->rethrow()->unwrap();

        expect($checkCharlieReadsHandbook->getAllowed())->toBeTrue();
    });

    test('expands permissions', function (): void {
        $expandResponse = $this->client->expand(
            store: $this->storeId,
            tupleKey: tuple('', 'reader', 'document:design'),
            model: $this->modelId,
        )->rethrow()->unwrap();

        $tree = $expandResponse->getTree();
        expect($tree)->not()->toBeNull();
        expect($tree->getRoot())->not()->toBeNull();
    });

    test('lists accessible objects', function (): void {
        $objectsResponse = $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        )->rethrow()->unwrap();

        $objects = $objectsResponse->getObjects();
        expect($objects)->toContain('document:readme');
        expect($objects)->toContain('document:design');

        $bobWriteResponse = $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            type: 'document',
            relation: 'writer',
            user: 'user:bob',
        )->rethrow()->unwrap();

        $bobObjects = $bobWriteResponse->getObjects();
        expect($bobObjects)->toContain('document:spec');
    });

    test('lists permitted users', function (): void {
        $userFilter = new UserTypeFilter(type: 'user');
        $userFilters = new UserTypeFilters;
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
            $userList[] = is_string($object) ? $object : (string) $object;
        }

        expect($userList)->toContain('user:bob');
        expect($userList)->toContain('user:charlie');
    });

    test('contextual permissions with consistency', function (): void {
        $checkResponse = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'owner', 'document:readme'),
            consistency: Consistency::MINIMIZE_LATENCY,
        )->rethrow()->unwrap();

        expect($checkResponse->getAllowed())->toBeTrue();

        $strongCheckResponse = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:alice', 'owner', 'document:readme'),
            consistency: Consistency::HIGHER_CONSISTENCY,
        )->rethrow()->unwrap();

        expect($strongCheckResponse->getAllowed())->toBeTrue();
    });
});
