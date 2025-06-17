<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\{allowed, dsl, model, store, tuple, write};

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = store(
    name: 'example-document-system',
    client: $client,
);

echo "Created store: {$storeId}\n";

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

$modelId = model(
    model: $model,
    store: $storeId,
    client: $client,
);

echo "Created model: {$modelId}\n";

write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:alice', 'viewer', 'document:readme'),
);

echo "Granted alice viewer permission on readme\n";

$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:alice', 'viewer', 'document:readme'),
);

echo $canView ? '✅ Alice can view readme' : '❌ Access denied';
