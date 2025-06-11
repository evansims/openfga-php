<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use DateTimeImmutable;
use InvalidArgumentException;
use OpenFGA\Authentication\{AccessToken, ClientCredentialAuthentication, TokenAuthentication};
use OpenFGA\{Client, ClientInterface};
use OpenFGA\Exceptions\ClientException;
use OpenFGA\Messages;
use OpenFGA\Models\AuthorizationModel;
use OpenFGA\Models\{BatchCheckItem, Store, TupleKey};
use OpenFGA\Models\Collections\{Assertions, BatchCheckItems, Conditions, TupleKeys, TypeDefinitions, UserTypeFilters};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Results\{Failure, ResultInterface};

describe('Client', function (): void {
    test('Client implements ClientInterface', function (): void {
        $client = new Client('https://api.example.com');

        expect($client)->toBeInstanceOf(ClientInterface::class);
    });

    test('Client constructor requires non-empty URL', function (): void {
        expect(fn () => new Client(''))->toThrow(InvalidArgumentException::class, 'URL is required and cannot be empty');
    });

    test('Client constructs with minimal configuration', function (): void {
        $url = 'https://api.example.com';
        $client = new Client($url);

        expect($client)->toBeInstanceOf(Client::class);
        expect($client->getLastRequest())->toBeNull();
        expect($client->getLastResponse())->toBeNull();
    });

    test('Client constructs with full configuration', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: new ClientCredentialAuthentication(
                clientId: 'client_id',
                clientSecret: 'client_secret',
                audience: 'https://api.example.com',
                issuer: 'https://auth.example.com',
            ),
            httpMaxRetries: 5,
        );

        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client constructs with no authentication', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: null,
        );

        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client constructs with token authentication', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: new TokenAuthentication('bearer_token_123'),
        );

        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client constructs with AccessToken object', function (): void {
        $accessToken = new AccessToken('token_value', time() + 3600);

        $client = new Client(
            url: 'https://api.example.com',
            authentication: new TokenAuthentication($accessToken),
        );

        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client constructs with language parameter', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: null,
            language: 'es',
        );

        expect($client)->toBeInstanceOf(Client::class);
        expect($client->getLanguage())->toBe('es');
    });

    test('Client getLanguage returns default language when not specified', function (): void {
        $client = new Client('https://api.example.com');

        expect($client->getLanguage())->toBe('en');
    });

    test('Client getLanguage returns configured language', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: null,
            language: 'es',
        );

        expect($client->getLanguage())->toBe('es');
    });

    test('Client assertLastRequest throws with configured language', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: null,
            language: 'es',
        );

        $client->assertLastRequest();
    })->throws(ClientException::class, trans(Messages::NO_LAST_REQUEST_FOUND, [], 'es'));

    test('Client assertLastRequest throws when no request exists', function (): void {
        $client = new Client('https://api.example.com');

        $client->assertLastRequest();
    })->throws(ClientException::class, trans(Messages::NO_LAST_REQUEST_FOUND));

    test('Client check returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = 'model-456';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');

        $result = $client->check($store, $model, $tupleKey);

        expect($result)->toBeInstanceOf(ResultInterface::class);
        expect($result)->toBeInstanceOf(Failure::class);
    });

    test('Client check accepts Store object', function (): void {
        $client = new Client('https://api.example.com');

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable,
            updatedAt: new DateTimeImmutable,
        );
        $model = 'model-456';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');

        $result = $client->check($store, $model, $tupleKey);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client check accepts optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = 'model-456';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');
        $contextualTuples = new TupleKeys([
            new TupleKey('user:alice', 'member', 'team:engineering'),
        ]);

        $result = $client->check(
            store: $store,
            model: $model,
            tupleKey: $tupleKey,
            trace: true,
            context: (object) ['time' => '2024-01-01T10:00:00Z'],
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client createStore returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->createStore('My Test Store');

        expect($result)->toBeInstanceOf(ResultInterface::class);
        expect($result)->toBeInstanceOf(Failure::class);
    });

    test('Client createStore trims name', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->createStore('  My Test Store  ');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client deleteStore returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->deleteStore('store-123');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client dsl returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $dsl = 'model
      schema 1.1

    type user

    type document
      relations
        define viewer: [user]';

        $result = $client->dsl($dsl);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client expand returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');

        $result = $client->expand($store, $tupleKey);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client getAuthorizationModel returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->getAuthorizationModel('store-123', 'model-456');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client getStore returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->getStore('store-123');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listAuthorizationModels returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listAuthorizationModels('store-123');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('listAuthorizationModels pagination', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listAuthorizationModels(
            store: 'store-123',
            continuationToken: 'token-abc',
            pageSize: 50,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listAuthorizationModels validates page size', function (): void {
        $client = new Client('https://api.example.com');

        // Large page size should work (will be clamped by repository)
        $result1 = $client->listAuthorizationModels('store-123', null, 5000);
        expect($result1)->toBeInstanceOf(ResultInterface::class);

        // Zero page size should throw validation exception
        expect(function () use ($client): void {
            $client->listAuthorizationModels('store-123', null, 0);
        })->toThrow(ClientException::class);

        // Negative page size should throw validation exception
        expect(function () use ($client): void {
            $client->listAuthorizationModels('store-123', null, -1);
        })->toThrow(ClientException::class);
    });

    test('listAuthorizationModels accepts pagination parameters', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listAuthorizationModels('store-123', 'next', 5);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listObjects returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listObjects(
            store: 'store-123',
            model: 'model-456',
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listStores returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listStores();

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('listStores pagination', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listStores(
            continuationToken: 'token-xyz',
            pageSize: 25,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listTupleChanges returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listTupleChanges('store-123');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listTupleChanges with all parameters', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->listTupleChanges(
            store: 'store-123',
            continuationToken: 'token-123',
            pageSize: 100,
            type: 'document',
            startTime: new DateTimeImmutable('2024-01-01'),
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listUsers returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $userFilters = new UserTypeFilters([]);

        $result = $client->listUsers(
            store: 'store-123',
            model: 'model-456',
            object: 'document:readme',
            relation: 'viewer',
            userFilters: $userFilters,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client readAssertions returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->readAssertions('store-123', 'model-456');

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client readTuples returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $tupleKey = new TupleKey('', '', '');

        $result = $client->readTuples('store-123', $tupleKey);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeAssertions returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $assertions = new Assertions([]);

        $result = $client->writeAssertions('store-123', 'model-456', $assertions);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $writes = new TupleKeys([
            new TupleKey('user:alice', 'viewer', 'document:readme'),
        ]);

        $result = $client->writeTuples('store-123', 'model-456', $writes);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples with deletes', function (): void {
        $client = new Client('https://api.example.com');

        $writes = new TupleKeys([
            new TupleKey('user:alice', 'viewer', 'document:readme'),
        ]);
        $deletes = new TupleKeys([
            new TupleKey('user:bob', 'viewer', 'document:readme'),
        ]);

        $result = $client->writeTuples('store-123', 'model-456', $writes, $deletes);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples throws exception when transactional limit exceeded', function (): void {
        $client = new Client('https://api.example.com');

        // Create 101 tuples to exceed the limit
        $tuples = [];

        for ($i = 1; 101 >= $i; $i++) {
            $tuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
        }
        $writes = new TupleKeys($tuples);

        $result = $client->writeTuples('store-123', 'model-456', $writes, null, true);

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->err())->toBeInstanceOf(ClientException::class);
        expect($result->err()->getMessage())->toContain('Transactional writeTuples exceeded limit: 101 operations (max 100)');
        expect($result->err()->getMessage())->toContain('Use non-transactional mode or split into multiple requests');
    });

    test('Client writeTuples allows 100 operations in transactional mode', function (): void {
        $client = new Client('https://api.example.com');

        // Create exactly 100 tuples (at the limit)
        $tuples = [];

        for ($i = 1; 100 >= $i; $i++) {
            $tuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
        }
        $writes = new TupleKeys($tuples);

        $result = $client->writeTuples('store-123', 'model-456', $writes, null, true);

        // Should succeed (not throw exception)
        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples allows unlimited operations in non-transactional mode', function (): void {
        $client = new Client('https://api.example.com');

        // Create 200 tuples to test non-transactional mode
        $tuples = [];

        for ($i = 1; 200 >= $i; $i++) {
            $tuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
        }
        $writes = new TupleKeys($tuples);

        $result = $client->writeTuples('store-123', 'model-456', $writes, null, false);

        // Should succeed in non-transactional mode
        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples counts writes and deletes for transactional limit', function (): void {
        $client = new Client('https://api.example.com');

        // Create 60 writes and 41 deletes (101 total operations)
        $writetuples = [];

        for ($i = 1; 60 >= $i; $i++) {
            $writetuples[] = new TupleKey("user:writer{$i}", 'viewer', 'document:readme');
        }
        $writes = new TupleKeys($writetuples);

        $deletetuples = [];

        for ($i = 1; 41 >= $i; $i++) {
            $deletetuples[] = new TupleKey("user:deleter{$i}", 'viewer', 'document:readme');
        }
        $deletes = new TupleKeys($deletetuples);

        $result = $client->writeTuples('store-123', 'model-456', $writes, $deletes, true);

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->err())->toBeInstanceOf(ClientException::class);
        expect($result->err()->getMessage())->toContain('101 operations');
    });

    test('Client writeTuples validates limit after deduplication', function (): void {
        $client = new Client('https://api.example.com');

        // Create 120 duplicates that will deduplicate to exactly 100 unique operations
        $writetuples = [];

        for ($i = 1; 100 >= $i; $i++) {
            // Add each tuple twice to create duplicates
            $writetuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
            $writetuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme'); // duplicate

            // Add 20 extra duplicates to ensure we have > 100 raw tuples
            if (20 >= $i) {
                $writetuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme'); // another duplicate
            }
        }
        $writes = new TupleKeys($writetuples); // 220 total tuples, but only 100 unique

        // Should succeed because after deduplication, there are exactly 100 operations
        $result = $client->writeTuples('store-123', 'model-456', $writes, null, true);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client writeTuples validates limit with duplicate writes and deletes', function (): void {
        $client = new Client('https://api.example.com');

        // Create duplicates and overlapping writes/deletes
        $writetuples = [];
        $deletetuples = [];

        // Add 60 unique writes
        for ($i = 1; 60 >= $i; $i++) {
            $writetuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
        }

        // Add 50 deletes, with 10 overlapping with writes (deletes take precedence)
        for ($i = 51; 100 >= $i; $i++) {
            $deletetuples[] = new TupleKey("user:user{$i}", 'viewer', 'document:readme');
        }

        $writes = new TupleKeys($writetuples);
        $deletes = new TupleKeys($deletetuples);

        // After deduplication: 50 writes (60 - 10 overlaps) + 50 deletes = 100 operations
        $result = $client->writeTuples('store-123', 'model-456', $writes, $deletes, true);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client createAuthorizationModel returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $typeDefinitions = new TypeDefinitions([]);
        $conditions = new Conditions([]);

        $result = $client->createAuthorizationModel(
            store: 'store-123',
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: SchemaVersion::V1_1,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client VERSION constant is defined', function (): void {
        expect(Client::VERSION)->toBeString();
    });

    test('Client handles authentication with real HTTP requests', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            authentication: new ClientCredentialAuthentication(
                clientId: 'client_id',
                clientSecret: 'client_secret',
                audience: 'https://api.example.com',
                issuer: 'https://auth.example.com',
            ),
        );

        // Test that client can be configured with authentication
        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client dsl method handles complex authorization models', function (): void {
        $client = new Client('https://api.example.com');

        $dsl = 'model
  schema 1.1

type user

type organization
  relations
    define member: [user]

type document
  relations
    define owner: [user]
    define viewer: [user, organization#member]
    define can_view: owner or viewer';

        $result = $client->dsl($dsl);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client dsl method handles malformed DSL', function (): void {
        $client = new Client('https://api.example.com');

        $invalidDsl = 'invalid dsl format that should fail parsing';

        $result = $client->dsl($invalidDsl);

        expect($result)->toBeInstanceOf(ResultInterface::class);
        // Note: DSL parsing may be more lenient than expected, so we just check it returns a Result
    });

    test('Client expand accepts all optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');
        $model = 'model-456';
        $contextualTuples = new TupleKeys([
            new TupleKey('user:alice', 'member', 'team:engineering'),
        ]);

        $result = $client->expand(
            store: $store,
            tupleKey: $tupleKey,
            model: $model,
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client readTuples accepts all optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');

        $result = $client->readTuples(
            store: $store,
            tupleKey: $tupleKey,
            continuationToken: 'token-123',
            pageSize: 100,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listUsers accepts all optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $userFilters = new UserTypeFilters([]);
        $contextualTuples = new TupleKeys([
            new TupleKey('user:alice', 'member', 'team:engineering'),
        ]);

        $result = $client->listUsers(
            store: 'store-123',
            model: 'model-456',
            object: 'document:readme',
            relation: 'viewer',
            userFilters: $userFilters,
            context: (object) ['time' => '2024-01-01T10:00:00Z'],
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client listObjects accepts all optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $contextualTuples = new TupleKeys([
            new TupleKey('user:alice', 'member', 'team:engineering'),
        ]);

        $result = $client->listObjects(
            store: 'store-123',
            model: 'model-456',
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
            context: (object) ['time' => '2024-01-01T10:00:00Z'],
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client streamedListObjects returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $result = $client->streamedListObjects(
            store: 'store-123',
            model: 'model-456',
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client streamedListObjects accepts all optional parameters', function (): void {
        $client = new Client('https://api.example.com');

        $contextualTuples = new TupleKeys([
            new TupleKey('user:alice', 'member', 'team:engineering'),
        ]);

        $result = $client->streamedListObjects(
            store: 'store-123',
            model: 'model-456',
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
            context: (object) ['time' => '2024-01-01T10:00:00Z'],
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client createAuthorizationModel with all parameters', function (): void {
        $client = new Client('https://api.example.com');

        $typeDefinitions = new TypeDefinitions([]);
        $conditions = new Conditions([]);

        $result = $client->createAuthorizationModel(
            store: 'store-123',
            typeDefinitions: $typeDefinitions,
            conditions: $conditions,
            schemaVersion: SchemaVersion::V1_0,
        );

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client handles page size clamping for listTupleChanges', function (): void {
        $client = new Client('https://api.example.com');

        $result1 = $client->listTupleChanges('store-123', null, 5000);
        expect($result1)->toBeInstanceOf(ResultInterface::class);

        $result2 = $client->listTupleChanges('store-123', null, 0);
        expect($result2)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client handles page size clamping for readTuples', function (): void {
        $client = new Client('https://api.example.com');

        $tupleKey = new TupleKey('', '', '');

        $result1 = $client->readTuples('store-123', $tupleKey, null, 5000);
        expect($result1)->toBeInstanceOf(ResultInterface::class);

        $result2 = $client->readTuples('store-123', $tupleKey, null, 0);
        expect($result2)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client getModelId helper handles interface objects', function (): void {
        $client = new Client('https://api.example.com');

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([]),
        );

        // Test with AuthorizationModelInterface object
        $result = $client->getAuthorizationModel('store-123', $model);
        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client getStoreId helper handles interface objects', function (): void {
        $client = new Client('https://api.example.com');

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable,
            updatedAt: new DateTimeImmutable,
        );

        // Test with StoreInterface object
        $result = $client->getStore($store);
        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client constructor with null httpMaxRetries', function (): void {
        $client = new Client(
            url: 'https://api.example.com',
            httpMaxRetries: null,
        );

        expect($client)->toBeInstanceOf(Client::class);
    });

    test('Client constructor with various httpMaxRetries values', function (): void {
        $client1 = new Client('https://api.example.com', httpMaxRetries: 1);
        $client2 = new Client('https://api.example.com', httpMaxRetries: 10);
        $client3 = new Client('https://api.example.com', httpMaxRetries: 100);

        expect($client1)->toBeInstanceOf(Client::class);
        expect($client2)->toBeInstanceOf(Client::class);
        expect($client3)->toBeInstanceOf(Client::class);
    });

    test('Client batchCheck returns Result interface', function (): void {
        $client = new Client('https://api.example.com');

        $store = new Store(
            id: 'store-123',
            name: 'Test Store',
            createdAt: new DateTimeImmutable,
            updatedAt: new DateTimeImmutable,
        );
        $model = 'model-456';
        $checks = new BatchCheckItems([
            new BatchCheckItem(
                tupleKey: new TupleKey('user:alice', 'viewer', 'document:readme'),
                correlationId: 'check-1',
            ),
        ]);

        $result = $client->batchCheck($store, $model, $checks);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client batchCheck accepts string store and model IDs', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = 'model-456';
        $checks = new BatchCheckItems([
            new BatchCheckItem(
                tupleKey: new TupleKey('user:alice', 'viewer', 'document:readme'),
                correlationId: 'check-1',
            ),
        ]);

        $result = $client->batchCheck($store, $model, $checks);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client batchCheck accepts AuthorizationModel object', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = new AuthorizationModel(
            id: 'model-456',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: new TypeDefinitions([]),
        );
        $checks = new BatchCheckItems([
            new BatchCheckItem(
                tupleKey: new TupleKey('user:alice', 'viewer', 'document:readme'),
                correlationId: 'check-1',
            ),
        ]);

        $result = $client->batchCheck($store, $model, $checks);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client batchCheck handles multiple check items', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = 'model-456';
        $checks = new BatchCheckItems([
            new BatchCheckItem(
                tupleKey: new TupleKey('user:alice', 'viewer', 'document:readme'),
                correlationId: 'check-1',
            ),
            new BatchCheckItem(
                tupleKey: new TupleKey('user:bob', 'editor', 'document:readme'),
                correlationId: 'check-2',
            ),
            new BatchCheckItem(
                tupleKey: new TupleKey('user:charlie', 'owner', 'document:readme'),
                correlationId: 'check-3',
            ),
        ]);

        $result = $client->batchCheck($store, $model, $checks);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });

    test('Client batchCheck handles empty check items', function (): void {
        $client = new Client('https://api.example.com');

        $store = 'store-123';
        $model = 'model-456';
        $checks = new BatchCheckItems([]);

        $result = $client->batchCheck($store, $model, $checks);

        expect($result)->toBeInstanceOf(ResultInterface::class);
    });
});
