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

// example: config
use function OpenFGA\{tuple, tuples, writes};

// Prepare write and delete operations
$writeTuples = tuples(
    tuple(user: 'user:anne', relation: 'reader', object: 'document:budget'),
    tuple(user: 'user:bob', relation: 'editor', object: 'document:report'),
    // ... more write tuples
);

$deleteTuples = tuples(
    tuple(user: 'user:charlie', relation: 'viewer', object: 'document:old-doc'),
    // ... more delete tuples
);

// Fine-tune bulk write behavior for your specific needs
$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: $writeTuples,
    deletes: $deleteTuples,
    maxParallelRequests: 10, // Concurrent requests (default: 1)
    maxTuplesPerChunk: 50, // Tuples per request (max: 100)
    maxRetries: 3, // Retry attempts per chunk
    retryDelaySeconds: 1.0, // Initial retry delay
    stopOnFirstError: false, // Continue on failures
);

echo "Processed {$result->getTotalOperations()} operations\n";
echo 'Success rate: ' . ($result->getSuccessfulChunks() / $result->getTotalChunks() * 100) . "%\n";
// end-example: config
