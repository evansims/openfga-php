<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\{check, checks};

// Initialize the OpenFGA client
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// Store configuration
$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// Quick start: parallel permission checks with checks()
$results = checks(
    $client,
    $storeId,
    $modelId,
    check(user: 'user:alice', relation: 'viewer', object: 'document:budget'),
    check(user: 'user:alice', relation: 'editor', object: 'document:budget'),
    check(user: 'user:alice', relation: 'owner', object: 'document:budget'),
    check(user: 'user:bob', relation: 'viewer', object: 'document:budget'),
    check(user: 'user:bob', relation: 'editor', object: 'document:budget'),
    check(user: 'user:charlie', relation: 'viewer', object: 'document:report'),
);

// Process results - they maintain order
echo 'Permission check results:
';

foreach ($results as $correlationId => $allowed) {
    if ($allowed) {
        echo "✓ Check {$correlationId}: Allowed\n";
    } else {
        echo "✗ Check {$correlationId}: Denied\n";
    }
}
