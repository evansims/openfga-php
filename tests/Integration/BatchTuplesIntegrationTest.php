<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\{Client};
use OpenFGA\Models\Store;
use OpenFGA\Responses\{CreateAuthorizationModelResponse, WriteTuplesResponse};
use OpenFGA\Results\{FailureInterface, SuccessInterface};

use function OpenFGA\{tuple, tuples};

describe('WriteTuples Non-Transactional Integration', function (): void {
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

        // Create a temporary store for testing
        $storeResult = $this->client->createStore('integration-test-non-transactional-writes');

        if ($storeResult instanceof FailureInterface) {
            test()->markTestSkipped('Could not create test store: ' . $storeResult->err()->getMessage());
        }

        $this->store = $storeResult->unwrap();
        $this->storeId = $this->store->getId();

        // Create test authorization model
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
            define writer: [user]
            define owner: [user]
        ';

        $dslResult = $this->client->dsl($dsl);

        if ($dslResult instanceof FailureInterface) {
            test()->markTestSkipped('Could not parse DSL: ' . $dslResult->err()->getMessage());
        }

        $model = $dslResult->unwrap();
        $modelResult = $this->client->createAuthorizationModel(
            store: $this->storeId,
            typeDefinitions: $model->getTypeDefinitions(),
        );

        if ($modelResult instanceof FailureInterface) {
            test()->markTestSkipped('Could not create model: ' . $modelResult->err()->getMessage());
        }

        /** @var CreateAuthorizationModelResponse $modelResponse */
        $modelResponse = $modelResult->unwrap();
        $this->modelId = $modelResponse->getModel();
    });

    afterEach(function (): void {
        if (isset($this->storeId)) {
            $this->client->deleteStore($this->storeId);
        }
    });

    test('basic non-transactional write operation', function (): void {
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
        );

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $writes,
            transactional: false,
        );

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->isTransactional())->toBe(false);
        expect($response->getTotalOperations())->toBe(2);
        expect($response->isCompleteSuccess())->toBe(true);
        expect($response->getFailedChunks())->toBe(0);

        // Verify tuples were written
        $tupleResult = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:alice', 'reader', 'document:1'),
        );

        expect($tupleResult)->toBeInstanceOf(SuccessInterface::class);
        $tuples = $tupleResult->unwrap()->getTuples();
        expect($tuples)->toHaveCount(1);
    });

    test('large batch with chunking', function (): void {
        $writes = tuples();

        for ($i = 1; 150 >= $i; ++$i) {
            $writes[] = tuple("user:test{$i}", 'reader', "document:{$i}");
        }

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $writes,
            transactional: false,
            maxTuplesPerChunk: 50,
        );

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->isTransactional())->toBe(false);
        expect($response->getTotalOperations())->toBe(150);
        expect($response->getTotalChunks())->toBe(3); // 150 / 50 = 3 chunks
        expect($response->isCompleteSuccess())->toBe(true);
        expect($response->getSuccessRate())->toBe(1.0);
    });

    test('mixed writes and deletes', function (): void {
        // First write some tuples
        $initialWrites = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'reader', 'document:2'),
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $initialWrites,
        );

        // Now do mixed operation
        $writes = tuples(
            tuple('user:david', 'reader', 'document:4'),
            tuple('user:eve', 'reader', 'document:5'),
        );

        $deletes = tuples(
            tuple('user:bob', 'reader', 'document:2'),
        );

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $writes,
            deletes: $deletes,
            transactional: false,
        );

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(3); // 2 writes + 1 delete
        expect($response->isCompleteSuccess())->toBe(true);

        // Verify bob's tuple was deleted
        $bobResult = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:bob', 'reader', 'document:2'),
        );
        expect($bobResult->unwrap()->getTuples())->toHaveCount(0);

        // Verify new tuples were written
        $davidResult = $this->client->readTuples(
            store: $this->storeId,
            tupleKey: tuple('user:david', 'reader', 'document:4'),
        );
        expect($davidResult->unwrap()->getTuples())->toHaveCount(1);
    });

    test('parallel processing', function (): void {
        $writes = tuples();

        for ($i = 1; 100 >= $i; ++$i) {
            $writes[] = tuple("user:parallel{$i}", 'reader', "document:{$i}");
        }

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $writes,
            transactional: false,
            maxParallelRequests: 3,
            maxTuplesPerChunk: 25,
        );

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(100);
        expect($response->getTotalChunks())->toBe(4); // 100 / 25 = 4 chunks
        expect($response->isCompleteSuccess())->toBe(true);
    });

    test('partial failure handling', function (): void {
        // Try to write tuples with some invalid data
        $writes = tuples(
            tuple('user:alice', 'reader', 'document:1'),
            tuple('user:bob', 'invalid_relation', 'document:2'), // This should fail
            tuple('user:charlie', 'reader', 'document:3'),
        );

        $result = $this->client->writeTuples(
            store: $this->storeId,
            model: $this->modelId,
            writes: $writes,
            transactional: false,
            maxTuplesPerChunk: 1, // Force separate requests
            stopOnFirstError: false,
        );

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $response */
        $response = $result->unwrap();
        expect($response->getTotalOperations())->toBe(3);
        expect($response->getTotalChunks())->toBe(3);
        expect($response->isPartialSuccess())->toBe(true);
        expect($response->getSuccessfulChunks())->toBe(2);
        expect($response->getFailedChunks())->toBe(1);
        expect($response->getErrors())->toHaveCount(1);
    });
});
