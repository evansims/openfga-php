<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: client
use function OpenFGA\{tuple, tuples};

// Test "what-if" scenarios without permanently saving relationships
// What if anne was in the engineering team?
$wouldHaveAccess = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:anne', 'viewer', 'document:report'),
    contextualTuples: tuples(
        tuple('user:anne', 'member', 'team:engineering'),
    ),
);

if ($wouldHaveAccess->succeeded() && $wouldHaveAccess->unwrap()->getAllowed()) {
    echo "Anne would have viewer access through team membership\n";
} else {
    echo "Anne would not have viewer access through team membership\n";
}
// end-example: client

// Test multiple contextual tuples
$contextualCheck = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:bob', 'editor', 'document:roadmap'),
    contextualTuples: tuples(
        tuple('team:product#member', 'member', 'user:bob'),
        tuple('team:product#member', 'editor', 'document:roadmap'),
    ),
);

if ($contextualCheck->succeeded() && $contextualCheck->unwrap()->getAllowed()) {
    echo "Bob WOULD have editor access through team membership\n";
}
