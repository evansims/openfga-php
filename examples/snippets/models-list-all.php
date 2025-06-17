<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];

// example: helper
use function OpenFGA\models;

// List all models using the helper
$authModels = models($client, $storeId);

foreach ($authModels as $model) {
    echo "Model ID: {$model->getId()}\n";
    echo "Schema Version: {$model->getSchemaVersion()->value}\n\n";
}
// end-example: helper

// example: client
// List models manually with pagination
$pageSize = 10;
$continuationToken = null;

do {
    $result = $client->listAuthorizationModels(
        store: $storeId,
        pageSize: $pageSize,
        continuationToken: $continuationToken,
    );

    if ($result->succeeded()) {
        $response = $result->unwrap();
        $models = $response->getModels();

        foreach ($models as $model) {
            echo "Model ID: {$model->getId()}\n";
            echo "Schema Version: {$model->getSchemaVersion()->value}\n\n";
        }

        $continuationToken = $response->getContinuationToken();
    } else {
        echo "Error listing models: {$result->err()->getMessage()}\n";

        break;
    }
} while (null !== $continuationToken && '' !== $continuationToken);
// end-example: client

// example: specific
// Get a specific model and convert to DSL
if (isset($_ENV['FGA_MODEL_ID'])) {
    $modelResult = $client->getAuthorizationModel(
        store: $storeId,
        model: $_ENV['FGA_MODEL_ID'],
    );

    if ($modelResult->succeeded()) {
        $model = $modelResult->unwrap()->getModel();

        echo "Retrieved model: {$model->getId()}\n";
        echo "Types in model:\n";

        foreach ($model->getTypeDefinitions() as $typeDef) {
            echo "  - {$typeDef->getType()}\n";
        }
    }
}
// end-example: specific
