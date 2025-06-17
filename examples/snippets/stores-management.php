<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// example: seperation
use RuntimeException;
use Throwable;

use function OpenFGA\failure;

// Environment separation
function createEnvironmentStore(Client $client, string $environment): string
{
    $result = $client->createStore(
        name: sprintf('app-%s-%s', $environment, date('Y-m-d')),
    );

    failure($result, function (Throwable $e): void {
        throw new RuntimeException('Failed to create store: ' . $e->getMessage());
    });

    return $result->unwrap()->getId();
}
// end-example: seperation

// example: management
// Store management - list all stores
$result = $client->listStores();

if ($result->succeeded()) {
    $stores = $result->unwrap()->getStores();

    echo "Available stores:\n";

    foreach ($stores as $store) {
        echo "- {$store->getName()} (ID: {$store->getId()})\n";
        echo "  Created: {$store->getCreatedAt()->format('Y-m-d H:i:s')}\n";
        echo "  Updated: {$store->getUpdatedAt()->format('Y-m-d H:i:s')}\n";
    }
}
// end-example: management

// example: pagination
// Pagination with continuation tokens
$stores = [];
$continuationToken = null;

do {
    $result = $client->listStores(
        pageSize: 10,
        continuationToken: $continuationToken,
    );

    if ($result->succeeded()) {
        $response = $result->unwrap();

        foreach ($response->getStores() as $store) {
            $stores[] = $store;
        }
        $continuationToken = $response->getContinuationToken();
    } else {
        break;
    }
} while (null !== $continuationToken);

echo 'Total stores: ' . count($stores) . "\n";
// end-example: pagination
