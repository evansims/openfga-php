<?php

require __DIR__ . '/vendor/autoload.php';

use OpenFGA\API\Models\CreateStoreRequest;
use OpenFGA\Client;
use OpenFGA\SDK\Configuration\ClientConfiguration;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$credential = new ClientCredentialConfiguration(
    apiIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? null,
    apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? null,
    clientId: $_ENV['FGA_CLIENT_ID'] ?? null,
    clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? null,
);

$configuration = new ClientConfiguration(
    apiUrl: $_ENV['FGA_API_URL'] ?? null,
    storeId: $_ENV['FGA_STORE_ID'] ?? null,
    authorizationModelId: $_ENV['FGA_MODEL_ID'] ?? null,
    credentialConfiguration: $credential,
);

$client = new Client($configuration);

// $request = new CreateStoreRequest([
//     'name' => 'FGA Demo Store'
// ]);

// $response = $client->getAuthorizationModels();
$response = $client->getAuthorizationModel();

var_dump($response);
exit;
