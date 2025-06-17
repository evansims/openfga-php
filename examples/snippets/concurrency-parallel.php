<?php

declare(strict_types=1);

use OpenFGA\Client;

// Initialize the OpenFGA client
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// Store configuration
$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: parallelism
use function OpenFGA\{tuple, tuples, writes};

// Create some sample tuples
$tuplesToWrite = tuples(
    tuple('user:anne', 'reader', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'),
    tuple('user:charlie', 'viewer', 'document:report'),
    // ... imagine 1000 more tuples
);

// Sequential: ~10 seconds for 1000 tuples
$startTime = microtime(true);
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 1,
);
$sequentialTime = microtime(true) - $startTime;

// Parallel: ~2 seconds for 1000 tuples (5x faster!)
$startTime = microtime(true);
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $tuplesToWrite,
    maxParallelRequests: 10,
);
$parallelTime = microtime(true) - $startTime;

echo 'Sequential time: ' . round($sequentialTime, 2) . " seconds\n";
echo 'Parallel time: ' . round($parallelTime, 2) . " seconds\n";
echo 'Speed improvement: ' . round($sequentialTime / $parallelTime, 1) . "x faster!\n";
// end-example: parallelism
