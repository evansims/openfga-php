<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: write
use function OpenFGA\{delete, tuple, tuples, write};

// Granting permissions using the write() helper
write(
    tuples: tuple('user:anne', 'viewer', 'document:planning-doc'),
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "✓ Anne can now view the planning document\n";

// Grant multiple permissions at once
write(
    tuples: tuples(
        tuple('user:anne', 'viewer', 'document:planning-doc'),
        tuple('user:bob', 'editor', 'document:planning-doc'),
    ),
    client: $client,
    store: $storeId,
    model: $modelId,
);
// end-example: write

// example: delete
// Removing permissions using the delete() helper
delete(
    tuples: tuple('user:anne', 'viewer', 'document:planning-doc'),
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "✓ Anne's view access has been revoked\n";

// Remove multiple permissions
delete(
    tuples: tuples(
        tuple('user:anne', 'viewer', 'document:planning-doc'),
        tuple('user:bob', 'editor', 'document:planning-doc'),
    ),
    client: $client,
    store: $storeId,
    model: $modelId,
);
// end-example: delete
