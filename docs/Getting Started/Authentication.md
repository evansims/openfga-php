This guide shows you how to configure the SDK for various authentication methods and use cases.

## Configurations

### Client Credentials (OIDC)

This is the recommended authentication method when using the SDK with [Auth0 FGA](https://auth0.com/fine-grained-authorization). This configuration, the SDK handles OAuth token management automatically.

[Snippet](../../examples/snippets/authentication-client-credentials.php#intro)

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

[Snippet](../../examples/snippets/authentication-pre-shared-key.php#intro)

**Environment variables:**

```bash
FGA_API_URL=https://your-openfga-server.com
FGA_API_TOKEN=your_api_token
```

## Troubleshooting

### Authentication failed

- Verify your environment variables are set correctly
- Check that your client ID and secret are valid
- Ensure the issuer URL includes the full path (for example `/oauth/token`)

### Token expired errors

The SDK automatically refreshes tokens for Client Credentials authentication. If you're seeing expired token errors:

- Check your system clock is accurate
- Verify the audience URL is correct

### Local development issues

If authentication isn't working locally:

- Confirm your OpenFGA server allows unauthenticated requests
- Check the server logs for authentication requirements

### Environment variable loading

To load environment variables from a `.env` file, use a package like [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv):

```php
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}
```
