<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use Buzz\Client\FileGetContents;
use Nyholm\Psr7\Factory\Psr17Factory;
use OpenFGA\Client;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\TupleKey;

use function OpenFGA\{dsl, model, store};

/*
 * Non-Transactional Writes and Parallel Processing Example
 *
 * This example demonstrates Fiber-based parallel batch processing for
 * high-performance operations when dealing with large numbers of tuples.
 */

$storeId = null;
$client = null;

try {
    echo "⚡ Non-Transactional Writes & Parallel Processing Example\n\n";

    // Initialize client
    $client = new Client(
        url: 'http://localhost:8080',
        httpClient: new FileGetContents(new Psr17Factory),
        httpResponseFactory: new Psr17Factory,
        httpStreamFactory: new Psr17Factory,
        httpRequestFactory: new Psr17Factory,
    );

    // Create workspace
    echo "Setting up workspace...\n";
    $storeId = store($client, 'batch-processing-demo');

    $authModel = dsl($client, '
        model
          schema 1.1
        type user
        type document
          relations
            define viewer: [user]
    ');
    $modelId = model($client, $storeId, $authModel);

    echo "✅ Workspace ready\n\n";

    // Create a batch of tuple operations
    echo "📝 Creating batch of 50 tuple operations...\n";
    $writes = new TupleKeys([]);

    for ($i = 1; 50 >= $i; ++$i) {
        $writes->add(new TupleKey(
            user: "user:user{$i}",
            relation: 'viewer',
            object: "document:doc{$i}",
        ));
    }

    echo "✅ Created {$writes->count()} tuple operations\n\n";

    // Example 1: Sequential Processing (default)
    echo "🔄 Example 1: Sequential Processing\n";
    $startTime = microtime(true);

    $sequentialResult = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $writes,
        transactional: false,
        maxParallelRequests: 1,        // Sequential processing
        maxTuplesPerChunk: 10,         // 10 tuples per request
        maxRetries: 2,
        retryDelaySeconds: 0.1,
    )->unwrap();

    $sequentialTime = microtime(true) - $startTime;
    echo "✅ Sequential: {$sequentialResult->getTotalChunks()} chunks, " .
         'Success rate: ' . round($sequentialResult->getSuccessRate() * 100, 1) . '%, ' .
         'Time: ' . round($sequentialTime, 2) . "s\n\n";

    // Clear the tuples for next test
    $client->writeTuples(
        store: $storeId,
        model: $modelId,
        deletes: $writes,
    );

    // Example 2: Parallel Processing with Fibers
    echo "⚡ Example 2: Parallel Processing (Fibers)\n";
    $startTime = microtime(true);

    $parallelResult = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $writes,
        transactional: false,
        maxParallelRequests: 3,        // 3 concurrent requests via Fibers
        maxTuplesPerChunk: 10,         // 10 tuples per request
        maxRetries: 2,
        retryDelaySeconds: 0.1,
        stopOnFirstError: false,       // Continue processing even if some chunks fail
    )->unwrap();

    $parallelTime = microtime(true) - $startTime;
    echo "✅ Parallel: {$parallelResult->getTotalChunks()} chunks, " .
         'Success rate: ' . round($parallelResult->getSuccessRate() * 100, 1) . '%, ' .
         'Time: ' . round($parallelTime, 2) . "s\n\n";

    // Performance Analysis
    $speedup = 0 < $sequentialTime ? $sequentialTime / $parallelTime : 1;
    echo "📊 Performance Comparison:\n";
    echo '   Sequential time: ' . round($sequentialTime, 2) . "s\n";
    echo '   Parallel time:   ' . round($parallelTime, 2) . "s\n";
    echo '   Speedup:         ' . round($speedup, 2) . "x\n\n";

    // Example 3: Error Handling with Partial Success
    echo "🎯 Example 3: Partial Success Handling\n";

    // Create a mix of valid and potentially problematic operations
    $mixedWrites = new TupleKeys([
        new TupleKey('user:valid1', 'viewer', 'document:test1'),
        new TupleKey('user:valid2', 'viewer', 'document:test2'),
        new TupleKey('user:valid3', 'viewer', 'document:test3'),
    ]);

    $mixedResult = $client->writeTuples(
        store: $storeId,
        model: $modelId,
        writes: $mixedWrites,
        transactional: false,
        maxParallelRequests: 2,
        maxTuplesPerChunk: 2,
        maxRetries: 3,
        retryDelaySeconds: 0.05,
        stopOnFirstError: false,       // Allow partial success
    )->unwrap();

    echo "✅ Processed with robust error handling\n";
    echo '   Success rate: ' . round($mixedResult->getSuccessRate() * 100, 1) . "%\n";

    if ($mixedResult->isPartialSuccess()) {
        echo "⚠️  Some operations failed, but others succeeded\n";
        echo '   Errors: ' . count($mixedResult->getErrors()) . "\n";
    }

    echo "\n";

    echo "✨ Non-transactional writes demonstration complete!\n\n";

    echo "🎯 Key Benefits:\n";
    echo "   • True concurrency with PHP Fibers\n";
    echo "   • Configurable parallelism (1-N concurrent requests)\n";
    echo "   • Automatic retry with exponential backoff\n";
    echo "   • Partial success handling for robust operations\n";
    echo "   • Efficient resource utilization\n\n";

    echo "⚙️  Configuration Options:\n";
    echo "   • maxParallelRequests: Control concurrency level\n";
    echo "   • maxTuplesPerChunk: Optimize request sizes\n";
    echo "   • stopOnFirstError: Choose fail-fast vs partial success\n";
    echo "   • maxRetries: Handle transient failures\n";
} catch (Throwable $e) {
    echo '❌ Error: ' . $e->getMessage() . "\n";
    echo '💡 Make sure OpenFGA is running on http://localhost:8080' . "\n";
    echo '   You can start it with: docker run -p 8080:8080 openfga/openfga run' . "\n";

    exit(1);
} finally {
    // Clean up the store regardless of success or failure
    if (null !== $storeId && null !== $client) {
        try {
            echo "\n🧹 Cleaning up...\n";
            $client->deleteStore(store: $storeId);
            echo "✅ Store deleted successfully\n";
        } catch (Throwable $cleanupError) {
            echo '⚠️  Failed to delete store: ' . $cleanupError->getMessage() . "\n";
        }
    }
}
