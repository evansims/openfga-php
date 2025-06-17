<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);

// example: usage
use OpenFGA\Models\Store;

use function OpenFGA\{failure, success};

$result = $client->createStore(name: 'my-app-store');

failure($result, function (Throwable $e): void {
    throw new RuntimeException("Error creating store: {$e->getMessage()}");
});

success($result, function (Store $store): void {
    echo "Created store: {$store->getId()}\n";
    echo "Store name: {$store->getName()}\n";
});
// end-example: usage
