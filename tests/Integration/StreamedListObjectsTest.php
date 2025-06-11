<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Generator;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\{Client, ClientInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{Store};
use OpenFGA\Responses\{CreateAuthorizationModelResponse, StreamedListObjectsResponse};
use OpenFGA\Results\FailureInterface;
use OpenFGA\Results\{SuccessInterface};

use function count;
use function OpenFGA\{tuple, tuples};

describe('StreamedListObjects Integration', function (): void {
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

        // Create a temporary store for testing
        $storeResult = $this->client->createStore('integration-test-streamed-list-objects');

        if ($storeResult instanceof FailureInterface) {
            test()->markTestSkipped('Could not create test store: ' . $storeResult->err()->getMessage());
        }

        $this->store = $storeResult->unwrap();
        $this->storeId = $this->store->getId();
    });

    afterEach(function (): void {
        // Clean up the test store
        if (isset($this->storeId)) {
            $this->client->deleteStore($this->storeId);
        }
    });

    test('streams objects that user has relationship with', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        // Create test data: authorization model using DSL
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        // Create authorization model
        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        // Create test relationships
        $tuples = tuples(
            tuple('user:alice', 'reader', 'document:test1'),
            tuple('user:alice', 'reader', 'document:test2'),
            tuple('user:alice', 'reader', 'document:test3'),
            tuple('user:bob', 'reader', 'document:test4'), // Different user
        );

        $writeResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $tuples,
        );

        if ($writeResult instanceof FailureInterface) {
            test()->fail('Failed to write tuples: ' . $writeResult->err()->getMessage());
        }
        expect($writeResult)->toBeInstanceOf(SuccessInterface::class);

        // Test streaming list objects
        $streamResult = $client->streamedListObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        );

        expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
        $streamGenerator = $streamResult->unwrap();
        expect($streamGenerator)->toBeInstanceOf(Generator::class);

        $objects = [];

        foreach ($streamGenerator as $streamedResponse) {
            expect($streamedResponse)->toBeInstanceOf(StreamedListObjectsResponse::class);
            $objects[] = $streamedResponse->getObject();
        }

        // Should return the 3 documents that alice has reader relationship with
        expect($objects)->toHaveCount(3);
        expect($objects)->toContain('document:test1');
        expect($objects)->toContain('document:test2');
        expect($objects)->toContain('document:test3');
        expect($objects)->not->toContain('document:test4'); // Bob's document

        // Clean up test data
        $deleteTuples = tuples(
            tuple('user:alice', 'reader', 'document:test1'),
            tuple('user:alice', 'reader', 'document:test2'),
            tuple('user:alice', 'reader', 'document:test3'),
            tuple('user:bob', 'reader', 'document:test4'),
        );

        $deleteResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: $deleteTuples,
        );
        expect($deleteResult)->toBeInstanceOf(SuccessInterface::class);
    });

    test('streams with contextual tuples', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        // Create authorization model with more complex relationships using DSL
        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
            define viewer: [user] or reader
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        // Create base relationships
        $baseTuples = tuples(
            tuple('user:alice', 'reader', 'document:ctx1'),
        );

        $writeResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $baseTuples,
        );
        expect($writeResult)->toBeInstanceOf(SuccessInterface::class);

        // Use contextual tuples to temporarily grant additional access
        $contextualTuples = tuples(
            tuple('user:alice', 'reader', 'document:ctx2'),
            tuple('user:alice', 'reader', 'document:ctx3'),
        );

        $streamResult = $client->streamedListObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'viewer',
            user: 'user:alice',
            contextualTuples: $contextualTuples,
        );

        expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
        $streamGenerator = $streamResult->unwrap();

        $objects = [];

        foreach ($streamGenerator as $streamedResponse) {
            $objects[] = $streamedResponse->getObject();
        }

        // Should include both stored and contextual relationships
        expect($objects)->toContain('document:ctx1'); // From stored tuple
        expect($objects)->toContain('document:ctx2'); // From contextual tuple
        expect($objects)->toContain('document:ctx3'); // From contextual tuple

        // Clean up
        $deleteResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: $baseTuples,
        );
        expect($deleteResult)->toBeInstanceOf(SuccessInterface::class);
    });

    test('streams with consistency settings', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        $tuples = tuples(
            tuple('user:alice', 'reader', 'document:consistent1'),
            tuple('user:alice', 'reader', 'document:consistent2'),
        );

        $writeResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $tuples,
        );
        expect($writeResult)->toBeInstanceOf(SuccessInterface::class);

        // Test with different consistency levels
        foreach ([Consistency::MINIMIZE_LATENCY, Consistency::HIGHER_CONSISTENCY] as $consistency) {
            $streamResult = $client->streamedListObjects(
                store: $storeId,
                model: $modelId,
                type: 'document',
                relation: 'reader',
                user: 'user:alice',
                consistency: $consistency,
            );

            expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

            /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
            $streamGenerator = $streamResult->unwrap();

            $objects = [];

            foreach ($streamGenerator as $streamedResponse) {
                $objects[] = $streamedResponse->getObject();
            }

            expect($objects)->toHaveCount(2);
            expect($objects)->toContain('document:consistent1');
            expect($objects)->toContain('document:consistent2');
        }

        // Clean up
        $deleteResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: $tuples,
        );
        expect($deleteResult)->toBeInstanceOf(SuccessInterface::class);
    });

    test('handles empty result stream', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        // Don't create any tuples - test empty result
        $streamResult = $client->streamedListObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:nobody',
        );

        expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
        $streamGenerator = $streamResult->unwrap();

        $objects = [];

        foreach ($streamGenerator as $streamedResponse) {
            $objects[] = $streamedResponse->getObject();
        }

        expect($objects)->toBeEmpty();
    });

    test('compares streaming vs non-streaming results', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        // Create test data
        $tuples = tuples(
            tuple('user:alice', 'reader', 'document:compare1'),
            tuple('user:alice', 'reader', 'document:compare2'),
            tuple('user:alice', 'reader', 'document:compare3'),
        );

        $writeResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $tuples,
        );
        expect($writeResult)->toBeInstanceOf(SuccessInterface::class);

        // Get results from regular listObjects
        $listResult = $client->listObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        );

        expect($listResult)->toBeInstanceOf(SuccessInterface::class);
        $listObjects = $listResult->unwrap()->getObjects();

        // Get results from streamedListObjects
        $streamResult = $client->streamedListObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        );

        expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
        $streamGenerator = $streamResult->unwrap();

        $streamObjects = [];

        foreach ($streamGenerator as $streamedResponse) {
            $streamObjects[] = $streamedResponse->getObject();
        }

        // Results should be identical (order may vary)
        sort($listObjects);
        sort($streamObjects);
        expect($streamObjects)->toBe($listObjects);

        // Clean up
        $deleteResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            deletes: $tuples,
        );
        expect($deleteResult)->toBeInstanceOf(SuccessInterface::class);
    });

    test('handles large result sets above 1000 items', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;

        $dsl = '
        model
          schema 1.1

        type user

        type document
          relations
            define reader: [user]
        ';

        $model = $client->dsl($dsl)->rethrow()->unwrap();

        $modelResult = $client->createAuthorizationModel($storeId, $model->getTypeDefinitions());
        expect($modelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $modelId = $createResponse->getModel();

        // Create a large number of tuples (1500 to exceed the 1000 limit)
        $numDocuments = 1500;
        $batchSize = 100; // Write in batches to avoid overwhelming the server

        echo "Creating {$numDocuments} document relationships in batches...\n";

        for ($batch = 0; $batch < ceil($numDocuments / $batchSize); $batch++) {
            $batchTuples = [];
            $startDoc = $batch * $batchSize;
            $endDoc = min(($batch + 1) * $batchSize, $numDocuments);

            for ($i = $startDoc; $i < $endDoc; $i++) {
                $batchTuples[] = tuple('user:alice', 'reader', "document:large-test-{$i}");
            }

            $writeResult = $client->writeTuples(
                store: $storeId,
                model: $modelId,
                writes: tuples(...$batchTuples),
            );

            if ($writeResult instanceof FailureInterface) {
                test()->fail("Failed to write batch {$batch}: " . $writeResult->err()->getMessage());
            }

            echo 'Written batch ' . ($batch + 1) . ' of ' . ceil($numDocuments / $batchSize) . "\n";
        }

        // Verify the regular listObjects is limited to 1000 results
        echo "Testing regular listObjects (should be limited to 1000)...\n";
        $listResult = $client->listObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        );

        expect($listResult)->toBeInstanceOf(SuccessInterface::class);
        $listObjects = $listResult->unwrap()->getObjects();

        // Regular listObjects should be limited to 1000 results
        expect(count($listObjects))->toBeLessThanOrEqual(1000);
        echo 'Regular listObjects returned ' . count($listObjects) . " results (correctly limited)\n";

        // Test streamedListObjects can handle all results
        echo "Testing streamedListObjects (should return all {$numDocuments} results)...\n";
        $streamResult = $client->streamedListObjects(
            store: $storeId,
            model: $modelId,
            type: 'document',
            relation: 'reader',
            user: 'user:alice',
        );

        expect($streamResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var Generator<StreamedListObjectsResponse> $streamGenerator */
        $streamGenerator = $streamResult->unwrap();
        expect($streamGenerator)->toBeInstanceOf(Generator::class);

        $streamObjects = [];
        $chunkCount = 0;

        foreach ($streamGenerator as $streamedResponse) {
            expect($streamedResponse)->toBeInstanceOf(StreamedListObjectsResponse::class);
            $streamObjects[] = $streamedResponse->getObject();
            $chunkCount++;

            // Progress indicator for large sets
            if (0 === $chunkCount % 500) {
                echo "Processed {$chunkCount} streaming results...\n";
            }
        }

        // StreamedListObjects should return ALL results (above the 1000 limit)
        expect(count($streamObjects))->toBe($numDocuments);
        echo 'StreamedListObjects returned ' . count($streamObjects) . " results (all items)\n";

        // Verify we got all the expected document IDs
        $expectedObjects = [];

        for ($i = 0; $i < $numDocuments; $i++) {
            $expectedObjects[] = "document:large-test-{$i}";
        }

        sort($streamObjects);
        sort($expectedObjects);
        expect($streamObjects)->toBe($expectedObjects);

        // Verify streamedListObjects returns more results than regular listObjects
        expect(count($streamObjects))->toBeGreaterThan(count($listObjects));
        expect(count($streamObjects))->toBeGreaterThan(1000);

        echo "✅ StreamedListObjects successfully handled {$numDocuments} results (exceeding 1000 limit)\n";
        echo '✅ Regular listObjects was properly limited to ' . count($listObjects) . " results\n";

        // Clean up - delete in batches
        echo "Cleaning up {$numDocuments} tuples in batches...\n";

        for ($batch = 0; $batch < ceil($numDocuments / $batchSize); $batch++) {
            $batchTuples = [];
            $startDoc = $batch * $batchSize;
            $endDoc = min(($batch + 1) * $batchSize, $numDocuments);

            for ($i = $startDoc; $i < $endDoc; $i++) {
                $batchTuples[] = tuple('user:alice', 'reader', "document:large-test-{$i}");
            }

            $deleteResult = $client->writeTuples(
                store: $storeId,
                model: $modelId,
                deletes: tuples(...$batchTuples),
            );

            expect($deleteResult)->toBeInstanceOf(SuccessInterface::class);
        }

        echo "Cleanup completed successfully\n";
    });
});
