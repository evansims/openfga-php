<?php

require __DIR__ . '/vendor/autoload.php';

use OpenFGA\API\Models\CreateStoreRequest;
use OpenFGA\Client;
use OpenFGA\Configuration;
use OpenFGA\Credentials\ClientCredential;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$credential = new ClientCredential(
    apiIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? null,
    apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? null,
    clientId: $_ENV['FGA_CLIENT_ID'] ?? null,
    clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? null,
);

$configuration = new Configuration(
    apiUrl: $_ENV['FGA_API_URL'] ?? null,
    storeId: $_ENV['FGA_STORE_ID'] ?? null,
    authorizationModelId: $_ENV['FGA_MODEL_ID'] ?? null,
    // credential: $credential,
);

$client = new Client($configuration);

// $request = new CreateStoreRequest([
//     'name' => 'FGA Demo Store'
// ]);

// $response = $client->getAuthorizationModels();
$response = $client->listStores();

var_dump($response);
exit;
