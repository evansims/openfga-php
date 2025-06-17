<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\{dsl, model};

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];

// example: conditions
// Creating a model with conditional types
$modelWithConditions = <<<'DSL'
        model
            schema 1.1

        type user

        type document
            relations
            define owner: [user]
            define viewer: [user] or owner
            define editor: [user] or owner
    DSL;

// Create model with conditions
$authorizationModel = dsl($modelWithConditions, $client);
$modelId = model($authorizationModel, $client, $storeId);

echo "Created model with conditional type: {$modelId}\n";
