<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use function OpenFGA\{allowed, tuple, tuples};

// Using the allowed() helper for simple checks
$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:budget',
);

if ($canView) {
    echo "Anne CAN view the budget document\n";
} else {
    echo "Anne CANNOT view the budget document\n";
}
// end-example: helper

// example: client
// Direct check method for more control
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:anne', 'viewer', 'document:roadmap'),
);

if ($result->succeeded()) {
    $response = $result->unwrap();

    if ($response->getAllowed()) {
        echo "Anne CAN view the roadmap\n";
    } else {
        echo "Anne CANNOT view the roadmap\n";
    }
} else {
    // Handle failure
    echo "Error checking permission\n";
}

// Check with contextual tuples
$contextualResult = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:anne', 'viewer', 'document:budget'),
    contextualTuples: tuples(
        tuple('group:finance#member', 'member', 'user:anne'),
        tuple('document:budget', 'parent', 'folder:finance'),
    ),
);

if ($contextualResult->succeeded() && $contextualResult->unwrap()->getAllowed()) {
    echo "Anne can view budget with additional context\n";
}
// end-example: client
