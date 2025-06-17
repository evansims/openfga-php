<?php

declare(strict_types=1);

use OpenFGA\Client;
use OpenFGA\Exceptions\{AuthenticationException, NetworkException};

use function OpenFGA\{allowed, tuple};

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);
$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: allowed
// Check permissions using the allowed() helper
$canEdit = allowed(
    user: 'user:anne',
    relation: 'editor',
    object: 'document:planning-doc',
    client: $client,
    store: $storeId,
    model: $modelId,
);

if ($canEdit) {
    echo "Anne CAN edit the planning document\n";
} else {
    echo "Anne CANNOT edit the planning document\n";
}

// Check viewer permissions
$canView = allowed(
    user: 'user:contractor',
    relation: 'viewer',
    object: 'document:project-spec',
    client: $client,
    store: $storeId,
    model: $modelId,
);

echo "Created model with advanced permissions: {$modelId}\n";
// end-example: allowed

// example: client
// Check permissions using the client directly
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:bob', 'viewer', 'document:strategy-2024'),
);

// Handle the result
if ($result->succeeded()) {
    $response = $result->unwrap();

    if ($response->getAllowed()) {
        echo "Bob CAN view the document\n";
    } else {
        echo "Bob CANNOT view the document\n";
    }
} else {
    $error = $result->err();
    echo "Error checking permission: {$error->getMessage()}\n";

    // Check specific error types
    if ($error instanceof NetworkException) {
        echo "Network issue - retry later\n";
    } elseif ($error instanceof AuthenticationException) {
        echo "Authentication failed - check credentials\n";
    }
}
// end-example: client
