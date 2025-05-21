<?php

require __DIR__ . '/vendor/autoload.php';

use OpenFGA\{Client, Authentication};

$client = new Client(
    url: getenv('FGA_API_URL') ?: 'http://localhost:8080',
    authentication: Authentication::TOKEN,
    token: getenv('FGA_SHARED_KEY') ?: null,
);

$response = $client->createStore(name: 'my-store');
$storeId = $response->getId();

$stores = $client->listStores();

foreach ($stores as $store) {
    $store->getId();
}

$client->deleteStore($storeId);
