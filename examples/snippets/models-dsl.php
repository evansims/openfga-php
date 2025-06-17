<?php

declare(strict_types=1);

use OpenFGA\Client;

use function OpenFGA\{dsl, model};

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];

// example: dsl
// Creating a model from DSL
$dslContent = <<<'DSL'
        model
            schema 1.1

        type user

        type document
            relations
            define owner: [user]
            define editor: [user] or owner
            define viewer: [user] or editor
            define can_delete: owner
            define can_edit: editor
            define can_view: viewer
    DSL;

// Convert DSL to authorization model
$authorizationModel = dsl($dslContent, $client);

// Create the model in your store
$modelId = model($authorizationModel, $client, $storeId);

echo "Created authorization model: {$modelId}\n";
// end-example: dsl
