<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\{BatchCheckItem, Store};
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Responses\BatchCheckResponseInterface;

use function OpenFGA\{tuple, tuples};

/*
 * Integration test for the batch-check API endpoint.
 *
 * This test verifies that the batch-check functionality works correctly
 * against a real OpenFGA instance, testing multiple authorization checks
 * in a single request.
 */
it('performs batch authorization checks successfully', function (): void {
    // Create OpenFGA client
    $client = new Client(
        url: $_ENV['OPENFGA_API_URL'] ?? 'http://openfga:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    // Create a store
    $storeResult = $client->createStore(name: 'batch-check-test-store');
    expect($storeResult->succeeded())->toBeTrue();

    $store = $storeResult->unwrap();
    $storeId = $store->getId();

    // Create an authorization model
    $modelDsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user] or writer
            define writer: [user] or owner
            define owner: [user]
    ';

    $authModelResult = $client->dsl($modelDsl);
    expect($authModelResult->succeeded())->toBeTrue();

    $authModel = $authModelResult->unwrap();

    $createModelResult = $client->createAuthorizationModel(
        store: $storeId,
        typeDefinitions: $authModel->getTypeDefinitions(),
        schemaVersion: $authModel->getSchemaVersion(),
    );
    expect($createModelResult->succeeded())->toBeTrue();

    $modelId = $createModelResult->unwrap()->getModel();

    // Write some tuples
    $writeTuplesResult = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: tuples(
            tuple('user:alice', 'owner', 'document:budget'),
            tuple('user:bob', 'reader', 'document:budget'),
            tuple('user:charlie', 'writer', 'document:spec'),
        ),
    );
    expect($writeTuplesResult->succeeded())->toBeTrue();

    // Create batch check items
    $batchItems = new BatchCheckItems;
    $batchItems->add(new BatchCheckItem(
        tupleKey: tuple('user:alice', 'reader', 'document:budget'),
        correlationId: 'alice-reader-budget',
    ));
    $batchItems->add(new BatchCheckItem(
        tupleKey: tuple('user:bob', 'writer', 'document:budget'),
        correlationId: 'bob-writer-budget',
    ));
    $batchItems->add(new BatchCheckItem(
        tupleKey: tuple('user:charlie', 'owner', 'document:spec'),
        correlationId: 'charlie-owner-spec',
    ));
    $batchItems->add(new BatchCheckItem(
        tupleKey: tuple('user:david', 'reader', 'document:budget'),
        correlationId: 'david-reader-budget',
    ));

    // Perform batch check
    $batchCheckResult = $client->batchCheck(
        store: $storeId,
        model: $modelId,
        checks: $batchItems,
    );

    expect($batchCheckResult->succeeded())->toBeTrue();

    $response = $batchCheckResult->unwrap();
    expect($response)->toBeInstanceOf(BatchCheckResponseInterface::class);

    // Verify results
    $results = $response->getResult();
    expect($results)->toHaveCount(4);

    // Alice has owner permission, so she should have reader access (inherited downward)
    $aliceResult = $response->getResultForCorrelationId('alice-reader-budget');
    expect($aliceResult)->not()->toBeNull();
    expect($aliceResult->getAllowed())->toBeTrue();

    // Bob has reader permission, so he should NOT have writer access (no inheritance upward)
    $bobResult = $response->getResultForCorrelationId('bob-writer-budget');
    expect($bobResult)->not()->toBeNull();
    expect($bobResult->getAllowed())->toBeFalse();

    // Charlie has writer permission, so he should NOT have owner access (no inheritance upward)
    $charlieResult = $response->getResultForCorrelationId('charlie-owner-spec');
    expect($charlieResult)->not()->toBeNull();
    expect($charlieResult->getAllowed())->toBeFalse();

    // David has no permissions, so should not have reader access
    $davidResult = $response->getResultForCorrelationId('david-reader-budget');
    expect($davidResult)->not()->toBeNull();
    expect($davidResult->getAllowed())->toBeFalse();

    // Clean up: delete the test store
    $deleteResult = $client->deleteStore(store: $storeId);
    expect($deleteResult->succeeded())->toBeTrue();
})->group('batch-check', 'integration');

it('handles batch check with contextual tuples', function (): void {
    // Create OpenFGA client
    $client = new Client(
        url: $_ENV['OPENFGA_API_URL'] ?? 'http://openfga:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    // Create a store
    $storeResult = $client->createStore(name: 'batch-check-contextual-test-store');
    expect($storeResult->succeeded())->toBeTrue();

    $store = $storeResult->unwrap();
    $storeId = $store->getId();

    // Create a simple authorization model
    $modelDsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
    ';

    $authModelResult = $client->dsl($modelDsl);
    expect($authModelResult->succeeded())->toBeTrue();

    $authModel = $authModelResult->unwrap();

    $createModelResult = $client->createAuthorizationModel(
        store: $storeId,
        typeDefinitions: $authModel->getTypeDefinitions(),
        schemaVersion: $authModel->getSchemaVersion(),
    );
    expect($createModelResult->succeeded())->toBeTrue();

    $modelId = $createModelResult->unwrap()->getModel();

    // Create batch check items with contextual tuples
    $batchItems = new BatchCheckItems;
    $batchItems->add(new BatchCheckItem(
        tupleKey: tuple('user:eve', 'reader', 'document:temp'),
        correlationId: 'eve-reader-temp',
        contextualTuples: tuples(
            tuple('user:eve', 'reader', 'document:temp'),
        ),
    ));

    // Perform batch check
    $batchCheckResult = $client->batchCheck(
        store: $storeId,
        model: $modelId,
        checks: $batchItems,
    );

    expect($batchCheckResult->succeeded())->toBeTrue();

    $response = $batchCheckResult->unwrap();
    $results = $response->getResult();
    expect($results)->toHaveCount(1);

    // Eve should have access via contextual tuple
    $eveResult = $response->getResultForCorrelationId('eve-reader-temp');
    expect($eveResult)->not()->toBeNull();
    expect($eveResult->getAllowed())->toBeTrue();

    // Clean up
    $deleteResult = $client->deleteStore(store: $storeId);
    expect($deleteResult->succeeded())->toBeTrue();
})->group('batch-check', 'integration', 'contextual');

it('validates correlation ID format', function (): void {
    expect(function (): void {
        new BatchCheckItem(
            tupleKey: tuple('user:test', 'reader', 'document:test'),
            correlationId: 'this-correlation-id-is-way-too-long-and-exceeds-the-36-character-limit',
        );
    })->toThrow(ClientException::class);
})->group('batch-check', 'validation');

it('validates correlation ID characters', function (): void {
    expect(function (): void {
        new BatchCheckItem(
            tupleKey: tuple('user:test', 'reader', 'document:test'),
            correlationId: 'invalid@correlation!id',
        );
    })->toThrow(ClientException::class);
})->group('batch-check', 'validation');
