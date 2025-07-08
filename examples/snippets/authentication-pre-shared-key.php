<?php

declare(strict_types=1);

// intro
use OpenFGA\Authentication\TokenAuthentication;
use OpenFGA\Client;

// Configure OpenFGA client with pre-shared API key
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    authentication: new TokenAuthentication(
        token: $_ENV['FGA_API_TOKEN'] ?? 'test-api-token',
    ),
);

// example: debug
// The token will be sent in the Authorization header
echo "✅ Client configured with API key authentication\n";
// end-example: debug
