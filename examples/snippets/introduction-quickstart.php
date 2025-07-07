<?php

declare(strict_types=1);

// intro
use OpenFGA\Client;

use function OpenFGA\{allowed, dsl, model, store, tuple, write};

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// example: create-a-store
$storeId = store(
    name: 'example-document-system',
    client: $client,
);

echo "Created store: {$storeId}\n";
// end-example: create-a-store

// example: define-a-model
$dsl = <<<'DSL'
        model
            schema 1.1

        type user

        type document
            relations
            define viewer: [user]
            define editor: [user]
    DSL;

$model = dsl(
    dsl: $dsl,
    client: $client,
);
// end-example: define-a-model

// example: create-a-model
$modelId = model(
    model: $model,
    store: $storeId,
    client: $client,
);

echo "Created model: {$modelId}\n";
// end-example: create-a-model

// example: grant-permission
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:alice', 'viewer', 'document:readme'),
);

echo "Granted alice viewer permission on readme\n";
// end-example: grant-permission

// example: check-permission
$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:alice', 'viewer', 'document:readme'),
);

echo $canView ? '✅ Alice can view readme' : '❌ Access denied';
// end-example: check-permission
