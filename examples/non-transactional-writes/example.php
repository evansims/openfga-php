<?php

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';

use OpenFGA\Authentication\ClientCredentialAuthentication;
use OpenFGA\{Client, Helpers};
use OpenFGA\Models\{BatchTupleOptions, TupleKey};
use OpenFGA\Models\Collections\TupleKeys;

// Example demonstrating Fiber-based parallel batch processing

echo "🚀 OpenFGA PHP SDK - Fiber-Based Parallel Batch Processing Example\n\n";

// Configuration
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    authentication: new ClientCredentialAuthentication(
        apiTokenIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? 'http://localhost:8080',
        apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? 'http://localhost:8080',
        clientId: $_ENV['FGA_CLIENT_ID'] ?? '',
        clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? '',
    ),
);

try {
    // Create a large batch of tuple operations
    $writes = new TupleKeys([]);

    for ($i = 1; 250 >= $i; ++$i) {
        $writes->add(new TupleKey(
            user: "user:user{$i}",
            relation: 'viewer',
            object: "document:doc{$i}",
        ));
    }

    echo "📝 Created {$writes->count()} tuple write operations\n\n";

    // Test 1: Sequential Processing (baseline)
    echo "🔄 Test 1: Sequential Processing\n";
    $startTime = microtime(true);

    $sequentialOptions = new BatchTupleOptions(
        maxParallelRequests: 1,        // Sequential
        maxTuplesPerChunk: 50,         // 50 tuples per chunk
        maxRetries: 2,
        retryDelaySeconds: 0.1,
    );

    $sequentialResult = Helpers\batch(
        client: $client,
        store: 'your-store-id',
        model: 'your-model-id',
        writes: $writes,
        options: $sequentialOptions,
    );

    $sequentialTime = microtime(true) - $startTime;
    echo "✅ Sequential: {$sequentialResult->getTotalChunks()} chunks, " .
         'Success rate: ' . round($sequentialResult->getSuccessRate() * 100, 1) . '%, ' .
         'Time: ' . round($sequentialTime, 2) . "s\n\n";

    // Test 2: Fiber-Based Parallel Processing
    echo "⚡ Test 2: Fiber-Based Parallel Processing\n";
    $startTime = microtime(true);

    $parallelOptions = new BatchTupleOptions(
        maxParallelRequests: 5,        // 5 concurrent requests via Fibers
        maxTuplesPerChunk: 50,         // 50 tuples per chunk
        maxRetries: 2,
        retryDelaySeconds: 0.1,
        stopOnFirstError: false,       // Continue processing even if some chunks fail
    );

    $parallelResult = Helpers\batch(
        client: $client,
        store: 'your-store-id',
        model: 'your-model-id',
        writes: $writes,
        options: $parallelOptions,
    );

    $parallelTime = microtime(true) - $startTime;
    echo "✅ Parallel: {$parallelResult->getTotalChunks()} chunks, " .
         'Success rate: ' . round($parallelResult->getSuccessRate() * 100, 1) . '%, ' .
         'Time: ' . round($parallelTime, 2) . "s\n\n";

    // Performance Analysis
    $speedup = 0 < $sequentialTime ? $sequentialTime / $parallelTime : 1;
    echo "📊 Performance Analysis:\n";
    echo '   Sequential time: ' . round($sequentialTime, 2) . "s\n";
    echo '   Parallel time:   ' . round($parallelTime, 2) . "s\n";
    echo '   Speedup:         ' . round($speedup, 2) . "x\n";
    echo '   Efficiency:      ' . round(($speedup / 5) * 100, 1) . "% (theoretical max: 5x)\n\n";

    // Test 3: High Concurrency with Error Handling
    echo "🎯 Test 3: High Concurrency with Advanced Options\n";

    $advancedOptions = new BatchTupleOptions(
        maxParallelRequests: 10,       // High concurrency
        maxTuplesPerChunk: 25,         // Smaller chunks for better error isolation
        maxRetries: 3,                 // More retries for reliability
        retryDelaySeconds: 0.05,       // Faster retries
        stopOnFirstError: false,       // Partial success handling
    );

    $startTime = microtime(true);
    $advancedResult = Helpers\batch(
        client: $client,
        store: 'your-store-id',
        model: 'your-model-id',
        writes: $writes,
        options: $advancedOptions,
    );
    $advancedTime = microtime(true) - $startTime;

    echo "✅ Advanced: {$advancedResult->getTotalChunks()} chunks, " .
         'Success rate: ' . round($advancedResult->getSuccessRate() * 100, 1) . '%, ' .
         'Time: ' . round($advancedTime, 2) . "s\n";

    if ($advancedResult->isPartialSuccess()) {
        echo "⚠️  Partial success - some chunks failed:\n";

        foreach ($advancedResult->getErrors() as $i => $error) {
            echo '   Error ' . ($i + 1) . ': ' . $error->getMessage() . "\n";
        }
    }

    echo "\n🎉 Fiber-based parallel processing demonstration complete!\n";
    echo "💡 Key benefits:\n";
    echo "   • True concurrency with PHP Fibers\n";
    echo "   • Configurable parallelism (1-N concurrent requests)\n";
    echo "   • Automatic retry with exponential backoff\n";
    echo "   • Partial success handling\n";
    echo "   • Efficient resource utilization\n";
} catch (Throwable $throwable) {
    echo '❌ Error: ' . $throwable->getMessage() . "\n";
    echo '📍 File: ' . $throwable->getFile() . ' Line: ' . $throwable->getLine() . "\n";
}
