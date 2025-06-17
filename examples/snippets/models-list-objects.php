<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\objects;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
// List objects a user can access using the helper
$documents = objects(
    type: 'document',
    relation: 'viewer',
    user: 'user:anne',
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "Anne can view the following documents:\n";

foreach ($documents as $document) {
    echo "- {$document}\n";
}
// end-example: helper

// example: client
// List objects using the client directly
$result = $client->streamedListObjects(
    store: $storeId,
    model: $modelId,
    user: 'user:bob',
    relation: 'editor',
    type: 'document',
);

if ($result->succeeded()) {
    $generator = $result->unwrap();
    $objects = [];

    foreach ($generator as $streamedResponse) {
        $objects[] = $streamedResponse->getObject();
    }

    echo 'Bob can edit ' . count($objects) . " documents:\n";

    foreach ($objects as $objectId) {
        echo "- {$objectId}\n";
    }
}
// end-example: client
