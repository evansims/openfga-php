<?php

declare(strict_types=1);

// intro
use OpenFGA\Authentication\ClientCredentialAuthentication;
use OpenFGA\Client;

// Configure OpenFGA client with OAuth2 client credentials
$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    authentication: new ClientCredentialAuthentication(
        clientId: $_ENV['FGA_CLIENT_ID'] ?? 'test-client-id',
        clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? 'test-client-secret',
        issuer: $_ENV['FGA_ISSUER'] ?? 'https://test-issuer.com',
        audience: $_ENV['FGA_AUDIENCE'] ?? 'https://test-audience.com',
    ),
);

// example: debug
// The client will automatically handle OAuth2 token exchange and renewal
echo "✅ Client configured with OAuth2 authentication\n";
// end-example: debug
