<?php

require __DIR__ . '/vendor/autoload.php';

use OpenFGA\Authentication\AuthenticationMode;
use OpenFGA\Client;

$client = new Client(
    url: getenv('FGA_API_URL') ?: 'http://localhost:8080',
    authenticationMode: AuthenticationMode::TOKEN,
    token: getenv('FGA_SHARED_KEY') ?: null,
);

$response = $client->createStore('FGA Demo Store');
$storeId = $response->getId();

$stores = $client->listStores();
var_dump($stores);

$client->deleteStore($storeId);
