<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];

// example: auditing
// Simple example to demonstrate listing changes
echo "Recent permission changes:\n";

$result = $client->listTupleChanges(
    store: $storeId,
    pageSize: 2,
);

if ($result->succeeded()) {
    $response = $result->unwrap();
    $changes = $response->getChanges();

    if (0 < count($changes)) {
        foreach ($changes as $change) {
            $tuple = $change->getTupleKey();
            $timestamp = $change->getTimestamp()->format('Y-m-d H:i:s');
            $operation = $change->getOperation()->value;

            echo "[{$timestamp}] {$operation}: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
        }
    } else {
        echo "No changes found in the store.\n";
    }
}
// end-example: auditing
