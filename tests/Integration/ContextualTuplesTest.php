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

describe('Contextual Tuples', function (): void {
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

        $name = 'contextual-test-' . bin2hex(random_bytes(5));
        $this->store = $this->client->createStore(name: $name)
            ->rethrow()
            ->unwrap();
        $this->storeId = $this->store->getId();

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

        $baseTuples = tuples(
            tuple('user:alice', 'member', 'organization:acme'),
            tuple('user:bob', 'member', 'organization:acme'),
            tuple('user:alice', 'owner', 'document:strategy'),
            tuple('organization:acme#member', 'viewer', 'document:public-doc'),
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
        if (isset($this->storeId)) {
            $this->client->deleteStore(store: $this->storeId);
        }
    });

    test('check with contextual tuples', function (): void {
        $checkWithoutContext = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:frank', 'viewer', 'document:public-doc'),
        )->rethrow()->unwrap();

        expect($checkWithoutContext->getAllowed())->toBeFalse();

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
        $withoutContext = $this->client->listObjects(
            store: $this->storeId,
            model: $this->modelId,
            type: 'document',
            relation: 'viewer',
            user: 'user:frank',
        )->rethrow()->unwrap();

        expect($withoutContext->getObjects())->toHaveCount(0);

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
        $userFilter = new UserTypeFilter(type: 'user');
        $userFilters = new UserTypeFilters;
        $userFilters->add($userFilter);

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
            $baseUsers[] = is_string($object) ? $object : (string) $object;
        }

        expect($baseUsers)->toContain('user:alice');
        expect($baseUsers)->toContain('user:bob');
        expect($baseUsers)->not->toContain('user:frank');

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
            $contextUsers[] = is_string($object) ? $object : (string) $object;
        }

        expect($contextUsers)->toContain('user:frank');
    });

    test('expand with contextual tuples', function (): void {
        $expandResponse = $this->client->expand(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('', 'viewer', 'document:public-doc'),
        )->rethrow()->unwrap();

        $tree = $expandResponse->getTree();
        expect($tree)->not()->toBeNull();
    });

    test('multiple contextual tuples', function (): void {
        $contextualTuples = tuples(
            tuple('user:grace', 'member', 'organization:acme'),
            tuple('user:grace', 'owner', 'document:temp-doc'),
        );

        $checkView = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:grace', 'viewer', 'document:public-doc'),
            contextualTuples: $contextualTuples,
        )->rethrow()->unwrap();

        expect($checkView->getAllowed())->toBeTrue();

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

        $checkMinLatency = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:henry', 'viewer', 'document:public-doc'),
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        )->rethrow()->unwrap();

        expect($checkMinLatency->getAllowed())->toBeTrue();

        $checkHighConsistency = $this->client->check(
            store: $this->storeId,
            model: $this->modelId,
            tupleKey: tuple('user:henry', 'viewer', 'document:public-doc'),
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        )->rethrow()->unwrap();

        expect($checkHighConsistency->getAllowed())->toBeTrue();
    });
});
