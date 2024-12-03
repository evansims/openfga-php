<?php

require __DIR__ . '/../../vendor/autoload.php';

use OpenFGA\API\Model\CreateStoreRequest;
use OpenFGA\Client;
use OpenFGA\SDK\Configuration\ClientConfiguration;
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;

$credential = new ClientCredentialConfiguration(
    apiIssuer: 'https://api.openfga.com',
    apiAudience: 'https://api.openfga.com',
    clientId: 'client',
    clientSecret: 'secret',
);

$configuration = new ClientConfiguration(
    apiUrl: 'https://api.openfga.com',
    storeId: 'store',
    authorizationModelId: 'model',
    credentialConfiguration: $credential,
);

$client = new Client($configuration);

$request = new CreateStoreRequest([
    'name' => 'FGA Demo Store'
]);

$response = $client->createStore($request);
