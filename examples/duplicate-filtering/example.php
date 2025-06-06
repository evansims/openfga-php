<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Responses\WriteTuplesResponse;

use function OpenFGA\{tuple, tuples};

require __DIR__ . '/../../vendor/autoload.php';

// Create client
$client = new Client(
    url: 'http://localhost:8080',
);

// Example 1: Duplicate writes are automatically filtered
$writes = tuples(
    tuple('user:anne', 'reader', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'),
    tuple('user:anne', 'reader', 'document:budget'), // duplicate - will be filtered out
    tuple('user:charlie', 'viewer', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'), // duplicate - will be filtered out
);

// Only 3 unique tuples will be written (anne, bob, charlie)
$result = $client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $writes,
);

if ($result->succeeded()) {
    echo "Successfully wrote unique tuples\n";
}

// Example 2: Delete takes precedence when tuple appears in both writes and deletes
$writes = tuples(
    tuple('user:anne', 'reader', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'),
    tuple('user:charlie', 'viewer', 'document:budget'),
);

$deletes = tuples(
    tuple('user:bob', 'editor', 'document:budget'), // This will remove bob from writes
    tuple('user:david', 'owner', 'document:old-file'),
);

// Result: writes anne and charlie, deletes bob and david
$result = $client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $writes,
    deletes: $deletes,
);

if ($result->succeeded()) {
    echo "Successfully processed tuples with delete precedence\n";
}

// Example 3: Non-transactional mode with duplicate filtering
$largeBatch = tuples(
    // ... imagine hundreds of tuples here, some duplicated
    tuple('user:anne', 'reader', 'document:1'),
    tuple('user:anne', 'reader', 'document:1'), // duplicate
    tuple('user:anne', 'reader', 'document:2'),
    // ... many more
);

// Duplicates are filtered before chunking, improving efficiency
$result = $client->writeTuples(
    store: 'store-id',
    model: 'model-id',
    writes: $largeBatch,
    transactional: false,
    maxParallelRequests: 5,
    maxTuplesPerChunk: 50,
);

if ($result->succeeded()) {
    /** @var WriteTuplesResponse $response */
    $response = $result->unwrap();
    echo sprintf(
        "Processed %d unique operations in %d chunks (%.1f%% success rate)\n",
        $response->getTotalOperations(),
        $response->getTotalChunks(),
        $response->getSuccessRate() * 100,
    );
}
