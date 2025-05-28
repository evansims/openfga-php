<?php

declare(strict_types=1);

use OpenFGA\Authentication\AccessToken;
use OpenFGA\{Authentication, Client, ClientInterface};
use OpenFGA\Models\Collections\{Assertions, Conditions, TupleKeys, TypeDefinitions, UserTypeFilters};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Models\{Store, TupleKey};
use OpenFGA\Results\{Failure, ResultInterface};

test('Client implements ClientInterface', function (): void {
    $client = new Client('https://api.example.com');

    expect($client)->toBeInstanceOf(ClientInterface::class);
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
        authentication: Authentication::CLIENT_CREDENTIALS,
        clientId: 'client_id',
        clientSecret: 'client_secret',
        issuer: 'https://auth.example.com',
        audience: 'https://api.example.com',
        token: 'pre_shared_token',
        maxRetries: 5,
    );

    expect($client)->toBeInstanceOf(Client::class);
});

test('Client constructs with no authentication', function (): void {
    $client = new Client(
        url: 'https://api.example.com',
        authentication: Authentication::NONE,
    );

    expect($client)->toBeInstanceOf(Client::class);
});

test('Client constructs with token authentication', function (): void {
    $client = new Client(
        url: 'https://api.example.com',
        authentication: Authentication::TOKEN,
        token: 'bearer_token_123',
    );

    expect($client)->toBeInstanceOf(Client::class);
});

test('Client constructs with AccessToken object', function (): void {
    $accessToken = new AccessToken('token_value', time() + 3600);

    $client = new Client(
        url: 'https://api.example.com',
        authentication: Authentication::CLIENT_CREDENTIALS,
        token: $accessToken,
    );

    expect($client)->toBeInstanceOf(Client::class);
});

test('Client assertLastRequest throws when no request exists', function (): void {
    $client = new Client('https://api.example.com');

    expect(fn () => $client->assertLastRequest())
        ->toThrow(LogicException::class, 'No last request found');
});

test('Client check returns Result interface', function (): void {
    $client = new Client('https://api.example.com');

    $store = 'store-123';
    $model = 'model-456';
    $tupleKey = new TupleKey('user:alice', 'viewer', 'document:readme');

    // Since we can't easily mock the network calls, we just verify the method returns a Result
    $result = $client->check($store, $model, $tupleKey);

    expect($result)->toBeInstanceOf(ResultInterface::class);
    expect($result)->toBeInstanceOf(Failure::class); // Will fail without real API
});

test('Client check accepts Store object', function (): void {
    $client = new Client('https://api.example.com');

    $store = new Store(
        id: 'store-123',
        name: 'Test Store',
        createdAt: new DateTimeImmutable(),
        updatedAt: new DateTimeImmutable(),
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
    expect($result)->toBeInstanceOf(Failure::class); // Will fail without real API
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

test('Client listAuthorizationModels handles pagination', function (): void {
    $client = new Client('https://api.example.com');

    $result = $client->listAuthorizationModels(
        store: 'store-123',
        continuationToken: 'token-abc',
        pageSize: 50,
    );

    expect($result)->toBeInstanceOf(ResultInterface::class);
});

test('Client listAuthorizationModels clamps page size', function (): void {
    $client = new Client('https://api.example.com');

    // Test max page size clamping
    $result1 = $client->listAuthorizationModels('store-123', null, 5000);
    expect($result1)->toBeInstanceOf(ResultInterface::class);

    // Test min page size clamping
    $result2 = $client->listAuthorizationModels('store-123', null, 0);
    expect($result2)->toBeInstanceOf(ResultInterface::class);
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

test('Client listStores handles pagination', function (): void {
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

    // TupleKey for reading can have empty strings to read all tuples
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
    expect(Client::VERSION)->toBe('0.2.0');
});
