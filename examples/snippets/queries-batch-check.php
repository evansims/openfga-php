<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use OpenFGA\Models\Collections\BatchCheckItems;

use function OpenFGA\{check, checks};

// Check multiple permissions using the checks() helper
$permissions = checks(
    $client,
    $storeId,
    $modelId,
    check(
        correlation: 'alice_budget_viewer',
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:budget',
    ),
    check(
        correlation: 'alice_budget_editor',
        user: 'user:alice',
        relation: 'editor',
        object: 'document:budget',
    ),
    check(
        correlation: 'bob_report_editor',
        user: 'user:bob',
        relation: 'editor',
        object: 'document:report',
    ),
);

// Process results
foreach ($permissions as $key => $allowed) {
    echo "{$key}: allowed = " . ($allowed ? 'true' : 'false') . "\n";
}
// end-example: helper

// example: client
// Batch check using client directly
$result = $client->batchCheck(
    store: $storeId,
    model: $modelId,
    checks: new BatchCheckItems([
        check(
            user: 'user:bob',
            relation: 'viewer',
            object: 'document:strategy',
        ),
        check(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:strategy',
        ),
        check(
            user: 'user:bob',
            relation: 'owner',
            object: 'document:strategy',
        ),
        check(
            user: 'user:bob',
            relation: 'viewer',
            object: 'document:roadmap',
        ),
    ]),
);

if ($result->succeeded()) {
    $response = $result->unwrap();
    $results = $response->getResult();

    foreach ($results as $correlationId => $checkResult) {
        if (null !== $checkResult->getError()) {
            echo "Check {$correlationId} failed: {$checkResult->getError()->getMessage()}\n";
        } else {
            $allowed = $checkResult->getAllowed();
            echo "Check {$correlationId}: " . ($allowed ? 'allowed' : 'denied') . "\n";
        }
    }
}
// end-example: client
