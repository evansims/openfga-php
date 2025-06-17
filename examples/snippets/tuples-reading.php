<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// Setup: Create some sample data for demonstration
use function OpenFGA\{tuple, tuples, write};

// Write some sample permissions
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        tuple('user:anne', 'viewer', 'document:planning-doc'),
        tuple('user:anne', 'editor', 'document:budget'),
        tuple('user:bob', 'editor', 'document:planning-doc'),
    ),
);

// example: helper
use function OpenFGA\read;

// Reading all tuples for a specific object
$tuples = read(
    client: $client,
    store: $storeId,
);

echo "Permissions for planning-doc:\n";

foreach ($tuples as $tuple) {
    if ('document:planning-doc' === $tuple->getObject()) {
        echo "- {$tuple->getUser()} has {$tuple->getRelation()} access\n";
    }
}
// end-example: helper

// example: client
// Reading permissions by user
$userResult = $client->readTuples(
    store: $storeId,
);

echo "\nAnne's permissions:\n";

if ($userResult->succeeded()) {
    $response = $userResult->unwrap();

    foreach ($response->getTuples() as $tuple) {
        if ('user:anne' === $tuple->getKey()->getUser()) {
            echo "- {$tuple->getKey()->getRelation()} on {$tuple->getKey()->getObject()}\n";
        }
    }
}
// end-example: client

// Find all documents a user can edit
$editableResult = $client->readTuples(
    store: $storeId,
);

echo "\nDocuments Bob can edit:\n";

if ($editableResult->succeeded()) {
    $response = $editableResult->unwrap();

    foreach ($response->getTuples() as $tuple) {
        if ('user:bob' === $tuple->getKey()->getUser()
            && 'editor' === $tuple->getKey()->getRelation()
            && str_starts_with($tuple->getKey()->getObject(), 'document:')) {
            echo "- {$tuple->getKey()->getObject()}\n";
        }
    }
}
