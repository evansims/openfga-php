# Authentication

Most OpenFGA servers require authentication, especially in production. This guide shows you how to configure authentication for different environments and use cases.

## TL;DR for busy developers

```php
// Production with client credentials (most common)
$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authentication: new ClientCredentialAuthentication(
        clientId: $_ENV['FGA_CLIENT_ID'],
        clientSecret: $_ENV['FGA_CLIENT_SECRET'],
        issuer: $_ENV['FGA_ISSUER'],
        audience: $_ENV['FGA_AUDIENCE'],
    ),
);

// Local development (no auth)
$client = new Client(url: 'http://localhost:8080');
```

## When do you need authentication?

- **Production environments** - Always required
- **Auth0 FGA** - Always required
- **Local development** - Usually optional

## Production Setup

### Client Credentials (Recommended)

This is the most common authentication method for production applications. The SDK handles OAuth token management automatically.

```php
use OpenFGA\Client;
use OpenFGA\Authentication\ClientCredentialAuthentication;

$client = new Client(
    url: $_ENV['FGA_API_URL'],
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

### API Token

For simpler setups or self-hosted OpenFGA instances that support API tokens:

```php
use OpenFGA\Client;
use OpenFGA\Authentication\TokenAuthentication;

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authentication: new TokenAuthentication($_ENV['FGA_API_TOKEN']),
);
```

**Environment variables:**

```bash
FGA_API_URL=https://your-openfga-server.com
FGA_API_TOKEN=your_api_token
```

## Development Setup

For local development against a Docker container or development server:

```php
use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);
```

## Client Configuration Patterns

### Using a factory method

```php
final readonly class FgaClientFactory
{
    public static function create(): Client
    {
        return match ($_ENV['APP_ENV'] ?? 'production') {
            'development', 'testing' => new Client(
                url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
            ),
            default => new Client(
                url: $_ENV['FGA_API_URL'],
                authentication: new ClientCredentialAuthentication(
                    clientId: $_ENV['FGA_CLIENT_ID'],
                    clientSecret: $_ENV['FGA_CLIENT_SECRET'],
                    issuer: $_ENV['FGA_ISSUER'],
                    audience: $_ENV['FGA_AUDIENCE'],
                ),
            ),
        };
    }
}
```

### Dependency injection

```php
// In your service container
$container->singleton(ClientInterface::class, function () {
    return new Client(
        url: config('fga.url'),
        authentication: new ClientCredentialAuthentication(
            clientId: config('fga.client_id'),
            clientSecret: config('fga.client_secret'),
            issuer: config('fga.issuer'),
            audience: config('fga.audience'),
        ),
    );
});
```

## Troubleshooting

### Authentication failed

- Verify your environment variables are set correctly
- Check that your client ID and secret are valid
- Ensure the issuer URL includes the full path (e.g., `/oauth/token`)

### Token expired errors

The SDK automatically refreshes tokens for Client Credentials authentication. If you're seeing expired token errors:

- Check your system clock is accurate
- Verify the audience URL matches your OpenFGA API endpoint exactly

### Local development issues

If authentication isn't working locally:

- Confirm your OpenFGA server allows unauthenticated requests
- Try using `NoAuthentication()` explicitly
- Check the server logs for authentication requirements

### Environment variable loading

Use a package like `vlucas/phpdotenv` for development:

```php
// Load .env file in development
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
```
