<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use function OpenFGA\{objects, tuple, tuples};

// List accessible objects using the helper
$documents = objects(
    'document',
    'viewer',
    'user:anne',
    $client,
    $storeId,
    $modelId,
);

echo "Documents Anne can view:\n";

foreach ($documents as $documentId) {
    echo "- {$documentId}\n";
}

// List objects with specific permissions
$editableDocuments = objects(
    'document',
    'editor',
    'user:anne',
    $client,
    $storeId,
    $modelId,
    null, // context
    tuples(
        tuple('team:engineering#member', 'member', 'user:anne'),
    ),
);
// end-example: helper

// example: client
// List objects using the client directly
$result = $client->streamedListObjects(
    store: $storeId,
    model: $modelId,
    user: 'user:bob',
    relation: 'owner',
    type: 'document',
);

if ($result->succeeded()) {
    $generator = $result->unwrap();
    $objects = [];

    foreach ($generator as $streamedResponse) {
        $objects[] = $streamedResponse->getObject();
    }

    echo 'Bob owns ' . count($objects) . " documents:\n";

    foreach ($objects as $object) {
        echo "- {$object}\n";
    }
}
// end-example: client
