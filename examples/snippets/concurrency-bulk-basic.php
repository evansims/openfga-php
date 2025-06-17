<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

$storeId = $_ENV['FGA_STORE_ID'];
$modelId = $_ENV['FGA_MODEL_ID'];

// example: helper
use function OpenFGA\{tuple, tuples, writes};

$writes = [];

for ($i = 0; 1000 > $i; $i++) {
    $writes[] = tuple(user: "user:user_{$i}", relation: 'reader', object: "document:doc_{$i}");
}

$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: tuples(...$writes),
);

echo "Processed {$result->getTotalOperations()} operations\n";
echo 'Success rate: ' . ($result->getSuccessfulChunks() / $result->getTotalChunks() * 100) . "%\n";
// end-example: helper

// example: api
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\TupleKey;
use OpenFGA\Responses\WriteTuplesResponseInterface;

$writes = [];
$deletes = [];

for ($i = 0; 1000 > $i; $i++) {
    $writes[] = new TupleKey(user: "user:user_{$i}", relation: 'reader', object: "document:doc_{$i}");
}

$result = $client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys(...$writes),
    transactional: false,
)
    ->success(function (WriteTuplesResponseInterface $result): void {
        echo "Processed {$result->getTotalOperations()} operations\n";
        echo 'Success rate: ' . ($result->getSuccessfulChunks() / $result->getTotalChunks() * 100) . "%\n";
    })
    ->failure(function (Throwable $error): void {
        echo "Error: {$error->getMessage()}\n";
    });
// end-example: api
