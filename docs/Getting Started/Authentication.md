This guide shows you how to configure the SDK for various authentication methods and use cases.

## Configurations

### Client Credentials (OIDC)

This is the recommended authentication method when using the SDK with [Auth0 FGA](https://auth0.com/fine-grained-authorization). The SDK handles OAuth token management automatically.

```php
use OpenFGA\Client;
use OpenFGA\Authentication\ClientCredentialAuthentication;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    authentication: new ClientCredentialAuthentication(
        clientId: $_ENV['FGA_CLIENT_ID'],
        clientSecret: $_ENV['FGA_CLIENT_SECRET'],
        issuer: $_ENV['FGA_ISSUER'],
        audience: $_ENV['FGA_AUDIENCE'],
    ),
);
```

**Environment variables:**

```bash
FGA_API_URL=https://api.us1.fga.dev
FGA_CLIENT_ID=your_client_id
FGA_CLIENT_SECRET=your_client_secret
FGA_ISSUER=https://your-tenant.us.auth0.com/oauth/token
FGA_AUDIENCE=https://api.us1.fga.dev/
```

### Pre-Shared Keys (API Tokens)

For simpler setups or self-hosted OpenFGA instances that support API tokens:

```php
use OpenFGA\Client;
use OpenFGA\Authentication\TokenAuthentication;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
    authentication: new TokenAuthentication($_ENV['FGA_API_TOKEN']),
);
```

**Environment variables:**

```bash
FGA_API_URL=https://your-openfga-server.com
FGA_API_TOKEN=your_api_token
```

### No Authentication

Unless configured, the SDK will fallback to operating without any authentication method. This is suiable for local development against a Docker container or development server:

```php
use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);
```

## Troubleshooting

### Authentication failed

- Verify your environment variables are set correctly
- Check that your client ID and secret are valid
- Ensure the issuer URL includes the full path (for example `/oauth/token`)

### Token expired errors

The SDK automatically refreshes tokens for Client Credentials authentication. If you're seeing expired token errors:

- Check your system clock is accurate
- Verify the audience URL matches your OpenFGA API endpoint exactly

### Local development issues

If authentication isn't working locally:

- Confirm your OpenFGA server allows unauthenticated requests
- Check the server logs for authentication requirements

### Handling authentication errors

For comprehensive error handling patterns including authentication failures, see the **Results** guide which covers specific error handling for authentication errors.

### Environment variable loading

Use a package like `vlucas/phpdotenv` for development:

```php
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
```
