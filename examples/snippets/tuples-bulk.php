<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use function OpenFGA\{tuple, tuples, writes};

// Bulk operations using the writes() helper
writes(
    $client,
    writes: tuples(
        // Add anne as viewer
        tuple('user:anne', 'viewer', 'document:roadmap'),
        // Add bob as editor
        tuple('user:bob', 'editor', 'document:roadmap'),
        // Make alice the owner
        tuple('user:alice', 'owner', 'document:roadmap'),
    ),
    store: $storeId,
    model: $modelId,
);

echo "✓ Bulk write completed successfully\n";
// end-example: helper

// For more control, use the client's writeTuples method directly
$result = $client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple('user:david', 'viewer', 'document:budget'),
        tuple('user:emma', 'editor', 'document:budget'),
    ),
);

if ($result->succeeded()) {
    echo "✓ Direct writeTuples completed successfully\n";
} else {
    echo "✗ Direct writeTuples failed\n";
}
