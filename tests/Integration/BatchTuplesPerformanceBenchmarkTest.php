<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Integration;

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\{Client, ClientInterface};
use OpenFGA\Models\Store;
use OpenFGA\Responses\{CreateAuthorizationModelResponse, WriteTuplesResponse};
use OpenFGA\Results\{FailureInterface, SuccessInterface};

use function OpenFGA\{tuple, tuples};

describe('BatchTuples Performance Benchmark', function (): void {
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
        $storeResult = $this->client->createStore('perf-test-batch-tuples');

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

        $model = $this->client->dsl($dsl)->rethrow()->unwrap();
        $modelResult = $this->client->createAuthorizationModel($this->storeId, $model->getTypeDefinitions());

        if ($modelResult instanceof FailureInterface) {
            test()->markTestSkipped('Could not create test model: ' . $modelResult->err()->getMessage());
        }

        /** @var CreateAuthorizationModelResponse $createResponse */
        $createResponse = $modelResult->unwrap();
        $this->modelId = $createResponse->getModel();
    });

    afterEach(function (): void {
        // Clean up the test store
        if (isset($this->storeId)) {
            $this->client->deleteStore($this->storeId);
        }
    });

    test('benchmarks sequential vs parallel processing with small batches', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;
        $modelId = $this->modelId;

        $numTuples = 50;
        $chunkSize = 10;

        echo "\n=== Small Batch Benchmark ({$numTuples} tuples, {$chunkSize} per chunk) ===\n";

        $writes = [];

        for ($i = 0; $i < $numTuples; $i++) {
            $writes[] = tuple("user:small{$i}", 'reader', "document:small{$i}");
        }

        $writeTuples = tuples(...$writes);

        // Benchmark sequential processing
        echo "Testing sequential processing...\n";
        $startTime = microtime(true);
        $sequentialResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: $chunkSize,
        );
        $sequentialTime = microtime(true) - $startTime;

        expect($sequentialResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $sequentialBatch */
        $sequentialBatch = $sequentialResult->unwrap();
        expect($sequentialBatch->isCompleteSuccess())->toBeTrue();

        echo 'Sequential: ' . number_format($sequentialTime, 3) . "s ({$sequentialBatch->getTotalChunks()} chunks)\n";

        // Clean up
        $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false);

        // Benchmark parallel processing (2 concurrent)
        echo "Testing parallel processing (2 concurrent)...\n";
        $startTime = microtime(true);
        $parallelResult2 = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: 2,
            maxTuplesPerChunk: $chunkSize,
        );
        $parallelTime2 = microtime(true) - $startTime;

        expect($parallelResult2)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $parallelBatch2 */
        $parallelBatch2 = $parallelResult2->unwrap();
        expect($parallelBatch2->isCompleteSuccess())->toBeTrue();

        echo 'Parallel (2): ' . number_format($parallelTime2, 3) . "s ({$parallelBatch2->getTotalChunks()} chunks)\n";

        // Clean up
        $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false);

        // Benchmark parallel processing (4 concurrent)
        echo "Testing parallel processing (4 concurrent)...\n";
        $startTime = microtime(true);
        $parallelResult4 = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: 4,
            maxTuplesPerChunk: $chunkSize,
        );
        $parallelTime4 = microtime(true) - $startTime;

        expect($parallelResult4)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $parallelBatch4 */
        $parallelBatch4 = $parallelResult4->unwrap();
        expect($parallelBatch4->isCompleteSuccess())->toBeTrue();

        echo 'Parallel (4): ' . number_format($parallelTime4, 3) . "s ({$parallelBatch4->getTotalChunks()} chunks)\n";

        // Clean up
        $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false);

        // Calculate improvements
        $improvement2 = 0 < $sequentialTime ? (($sequentialTime - $parallelTime2) / $sequentialTime) * 100 : 0;
        $improvement4 = 0 < $sequentialTime ? (($sequentialTime - $parallelTime4) / $sequentialTime) * 100 : 0;

        echo "Performance improvement:\n";
        echo '  2 concurrent: ' . number_format($improvement2, 1) . "%\n";
        echo '  4 concurrent: ' . number_format($improvement4, 1) . "%\n";

        // All configurations should complete successfully
        expect($sequentialTime)->toBeGreaterThan(0);
        expect($parallelTime2)->toBeGreaterThan(0);
        expect($parallelTime4)->toBeGreaterThan(0);
    });

    test('benchmarks sequential vs parallel processing with medium batches', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;
        $modelId = $this->modelId;

        $numTuples = 200;
        $chunkSize = 25;

        echo "\n=== Medium Batch Benchmark ({$numTuples} tuples, {$chunkSize} per chunk) ===\n";

        $writes = [];

        for ($i = 0; $i < $numTuples; $i++) {
            $writes[] = tuple("user:medium{$i}", 'reader', "document:medium{$i}");
        }

        $writeTuples = tuples(...$writes);

        // Benchmark sequential processing
        echo "Testing sequential processing...\n";
        $startTime = microtime(true);
        $sequentialResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: 1,
            maxTuplesPerChunk: $chunkSize,
        );
        $sequentialTime = microtime(true) - $startTime;

        expect($sequentialResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $sequentialBatch */
        $sequentialBatch = $sequentialResult->unwrap();
        expect($sequentialBatch->isCompleteSuccess())->toBeTrue();

        echo 'Sequential: ' . number_format($sequentialTime, 3) . "s ({$sequentialBatch->getTotalChunks()} chunks)\n";

        // Clean up
        $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false, maxParallelRequests: 4);

        // Benchmark parallel processing
        echo "Testing parallel processing (4 concurrent)...\n";
        $startTime = microtime(true);
        $parallelResult = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: 4,
            maxTuplesPerChunk: $chunkSize,
        );
        $parallelTime = microtime(true) - $startTime;

        expect($parallelResult)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $parallelBatch */
        $parallelBatch = $parallelResult->unwrap();
        expect($parallelBatch->isCompleteSuccess())->toBeTrue();

        echo 'Parallel (4): ' . number_format($parallelTime, 3) . "s ({$parallelBatch->getTotalChunks()} chunks)\n";

        // Clean up
        $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false, maxParallelRequests: 4);

        // Calculate throughput
        $sequentialThroughput = $numTuples / $sequentialTime;
        $parallelThroughput = $numTuples / $parallelTime;
        $improvement = 0 < $sequentialTime ? (($sequentialTime - $parallelTime) / $sequentialTime) * 100 : 0;

        echo "Throughput:\n";
        echo '  Sequential: ' . number_format($sequentialThroughput, 1) . " tuples/sec\n";
        echo '  Parallel:   ' . number_format($parallelThroughput, 1) . " tuples/sec\n";
        echo '  Improvement: ' . number_format($improvement, 1) . "%\n";

        expect($sequentialThroughput)->toBeGreaterThan(0);
        expect($parallelThroughput)->toBeGreaterThan(0);
    });

    test('benchmarks chunking strategies', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;
        $modelId = $this->modelId;

        $numTuples = 150;

        echo "\n=== Chunking Strategy Benchmark ({$numTuples} tuples) ===\n";

        $writes = [];

        for ($i = 0; $i < $numTuples; $i++) {
            $writes[] = tuple("user:chunk{$i}", 'reader', "document:chunk{$i}");
        }

        $writeTuples = tuples(...$writes);

        $chunkSizes = [10, 25, 50, 75];
        $results = [];

        foreach ($chunkSizes as $chunkSize) {
            echo "Testing chunk size {$chunkSize}...\n";

            $startTime = microtime(true);
            $result = $client->writeTuples(
                store: $storeId,
                model: $modelId,
                writes: $writeTuples,
                transactional: false,
                maxParallelRequests: 3,
                maxTuplesPerChunk: $chunkSize,
            );
            $duration = microtime(true) - $startTime;

            expect($result)->toBeInstanceOf(SuccessInterface::class);

            /** @var WriteTuplesResponse $batchResult */
            $batchResult = $result->unwrap();
            expect($batchResult->isCompleteSuccess())->toBeTrue();

            // Prevent division by zero for very fast operations
            $throughput = 0 < $duration ? $numTuples / $duration : 0;
            $results[$chunkSize] = [
                'duration' => $duration,
                'throughput' => $throughput,
                'chunks' => $batchResult->getTotalChunks(),
            ];

            echo '  Duration: ' . number_format($duration, 3) . "s\n";
            echo '  Throughput: ' . number_format($throughput, 1) . " tuples/sec\n";
            echo "  Chunks: {$batchResult->getTotalChunks()}\n";

            // Clean up
            $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false, maxParallelRequests: 4);
        }

        // Find the best performing chunk size
        $bestThroughput = 0;
        $bestChunkSize = array_keys($results)[0];

        foreach ($results as $chunkSize => $result) {
            if ($result['throughput'] > $bestThroughput) {
                $bestThroughput = $result['throughput'];
                $bestChunkSize = $chunkSize;
            }
        }
        echo "\nBest performing chunk size: {$bestChunkSize} (" .
             number_format($results[$bestChunkSize]['throughput'], 1) . " tuples/sec)\n";

        // All chunk sizes should work
        foreach ($results as $chunkSize => $result) {
            expect($result['duration'])->toBeGreaterThanOrEqual(0);
            expect($result['throughput'])->toBeGreaterThanOrEqual(0);
        }
    });

    test('measures fiber coordination overhead', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;
        $modelId = $this->modelId;

        $numTuples = 100;
        $chunkSize = 20;

        echo "\n=== Fiber Coordination Overhead Test ({$numTuples} tuples) ===\n";

        $writes = [];

        for ($i = 0; $i < $numTuples; $i++) {
            $writes[] = tuple("user:fiber{$i}", 'reader', "document:fiber{$i}");
        }

        $writeTuples = tuples(...$writes);

        // Test different levels of parallelism to see overhead
        $parallelismLevels = [1, 2, 4, 8];
        $results = [];

        foreach ($parallelismLevels as $parallelism) {
            echo "Testing {$parallelism} parallel requests...\n";

            $startTime = microtime(true);
            $result = $client->writeTuples(
                store: $storeId,
                model: $modelId,
                writes: $writeTuples,
                transactional: false,
                maxParallelRequests: $parallelism,
                maxTuplesPerChunk: $chunkSize,
            );
            $duration = microtime(true) - $startTime;

            expect($result)->toBeInstanceOf(SuccessInterface::class);

            /** @var WriteTuplesResponse $batchResult */
            $batchResult = $result->unwrap();
            expect($batchResult->isCompleteSuccess())->toBeTrue();

            // Prevent division by zero for very fast operations
            $throughput = 0 < $duration ? $numTuples / $duration : 0;
            $results[$parallelism] = [
                'duration' => $duration,
                'throughput' => $throughput,
            ];

            echo '  Duration: ' . number_format($duration, 3) . "s\n";
            echo '  Throughput: ' . number_format($throughput, 1) . " tuples/sec\n";

            // Clean up
            $client->writeTuples($storeId, $modelId, deletes: tuples(...$writes), transactional: false, maxParallelRequests: 4);
        }

        // Calculate efficiency relative to sequential
        $sequentialThroughput = $results[1]['throughput'];
        echo "\nEfficiency relative to sequential:\n";

        foreach ($parallelismLevels as $parallelism) {
            if (1 === $parallelism) continue;
            // Prevent division by zero
            $efficiency = 0 < $sequentialThroughput ? ($results[$parallelism]['throughput'] / $sequentialThroughput) / $parallelism * 100 : 0;
            echo "  {$parallelism}x parallel: " . number_format($efficiency, 1) . "% efficiency\n";
        }

        // All parallelism levels should complete successfully
        foreach ($results as $result) {
            expect($result['duration'])->toBeGreaterThanOrEqual(0);
            expect($result['throughput'])->toBeGreaterThanOrEqual(0);
        }
    });

    test('stress tests large batch with optimal settings', function (): void {
        /** @var ClientInterface $client */
        $client = $this->client;
        $storeId = $this->storeId;
        $modelId = $this->modelId;

        $numTuples = 500;
        $chunkSize = 50;
        $parallelism = 4;

        echo "\n=== Stress Test ({$numTuples} tuples, {$chunkSize} per chunk, {$parallelism} parallel) ===\n";

        $writes = [];

        for ($i = 0; $i < $numTuples; $i++) {
            $writes[] = tuple("user:stress{$i}", 'reader', "document:stress{$i}");
        }

        $writeTuples = tuples(...$writes);

        echo "Processing {$numTuples} tuples...\n";
        $startTime = microtime(true);
        $result = $client->writeTuples(
            store: $storeId,
            model: $modelId,
            writes: $writeTuples,
            transactional: false,
            maxParallelRequests: $parallelism,
            maxTuplesPerChunk: $chunkSize,
            maxRetries: 1,
            retryDelaySeconds: 0.1,
        );
        $duration = microtime(true) - $startTime;

        expect($result)->toBeInstanceOf(SuccessInterface::class);

        /** @var WriteTuplesResponse $batchResult */
        $batchResult = $result->unwrap();
        expect($batchResult->isCompleteSuccess())->toBeTrue();

        // Prevent division by zero for very fast operations
        $throughput = 0 < $duration ? $numTuples / $duration : 0;
        $expectedChunks = (int) ceil($numTuples / $chunkSize);

        echo "Results:\n";
        echo '  Duration: ' . number_format($duration, 3) . "s\n";
        echo '  Throughput: ' . number_format($throughput, 1) . " tuples/sec\n";
        echo "  Chunks: {$batchResult->getTotalChunks()} (expected: {$expectedChunks})\n";
        echo '  Success rate: ' . number_format($batchResult->getSuccessRate() * 100, 1) . "%\n";

        expect($batchResult->getTotalChunks())->toBe($expectedChunks);
        expect($batchResult->getSuccessfulChunks())->toBe($expectedChunks);
        expect($batchResult->getFailedChunks())->toBe(0);
        expect($throughput)->toBeGreaterThanOrEqual(0);

        // Should process at a reasonable rate (at least 10 tuples/sec) if duration > 0
        if (0 < $duration) {
            expect($throughput)->toBeGreaterThan(10);
        }

        // Clean up
        echo "Cleaning up {$numTuples} tuples...\n";
        $cleanupResult = $client->writeTuples(
            $storeId,
            $modelId,
            deletes: tuples(...$writes),
            transactional: false,
            maxParallelRequests: $parallelism,
            maxTuplesPerChunk: $chunkSize,
        );
        expect($cleanupResult)->toBeInstanceOf(SuccessInterface::class);
        echo "Cleanup completed successfully\n";
    });
});
