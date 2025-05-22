# Authenticating with OpenFGA

To interact with an OpenFGA server, your application (via the PHP SDK) needs to authenticate itself, unless the server is configured to allow anonymous access (common in some local development setups). This SDK provides several methods to handle authentication.

While local or development OpenFGA instances (like a basic Docker container) might not require authentication, production environments and services like Auth0 FGA typically do. Choosing the right authentication method depends on your OpenFGA server's configuration.

## 1. No Authentication

If your OpenFGA server does not require authentication, you only need to provide the server's URL when creating the client.

**Configuration:**

```php
<?php
use OpenFGA\Client;

// Assumes $client is initialized as shown in GettingStarted.md
// $fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080'; // Default for local Docker
// $client = new Client(url: $fgaApiUrl);

// Example:
$client = new Client(url: 'http://localhost:8080');

echo "Client configured for No Authentication, connecting to: " . $client->getUrl() . "\n";
// You can now use $client for operations if the server allows anonymous access.
?>
```

**Use Cases:**
*   Local development against a Dockerized OpenFGA instance where authentication has not been enabled.
*   Internal testing environments where the OpenFGA server is within a trusted network and configured for no auth.

## 2. API Token (Pre-Shared Key) Authentication

This method uses a static, pre-shared secret key (often referred to as an API token) that the SDK includes in the `Authorization` header of each request to the OpenFGA server.

**Configuration:**

To use API Token authentication, you need to:
1. Set the `authentication` parameter in the `Client` constructor to `OpenFGA\Enum\Authentication::TOKEN`.
2. Provide the API token via the `token` parameter.

It is strongly recommended to use environment variables to store the API token for security and flexibility, rather than hardcoding it.

```php
<?php
use OpenFGA\Client;
use OpenFGA\Enum\Authentication; // Enum for authentication methods

// Recommended: Load token from an environment variable
$apiToken = $_ENV['OPENFGA_API_TOKEN'] ?? 'your_fallback_token_here'; // Replace with your actual token or ensure ENV is set

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080', // Your OpenFGA server URL
    authentication: Authentication::TOKEN,
    token: $apiToken
);

echo "Client configured for API Token Authentication.\n";
// $client is now ready to make authenticated requests.
?>
```

**Suggested Environment Variable:**
*   `OPENFGA_API_TOKEN` or `FGA_API_TOKEN`

**Use Cases:**
*   Simpler setups where managing an OAuth flow is overly complex.
*   Some self-hosted OpenFGA deployments where API key authentication is configured.
*   Scenarios where you need a long-lived, static credential for server-to-server communication with OpenFGA.

## 3. Client Credentials Authentication

This method uses the OAuth 2.0 Client Credentials Grant flow. The SDK dynamically requests an access token from a specified identity provider (issuer) using a `clientId` and `clientSecret`. This access token is then automatically included in requests to the OpenFGA server and managed (e.g., refreshed) by the SDK.

**Configuration:**

To use Client Credentials authentication:
1. Set the `authentication` parameter to `OpenFGA\Enum\Authentication::CLIENT_CREDENTIALS`.
2. Provide the following required parameters:
    *   `clientId` (string): The client ID for your application, registered with the OAuth issuer.
    *   `clientSecret` (string): The client secret for your application.
    *   `issuer` (string): The URL of the OAuth token issuer (e.g., `https://your-auth0-domain.us.auth0.com/oauth/token` or your internal OAuth provider's token endpoint).
    *   `audience` (string): The audience identifier for the access token, typically the URL of your OpenFGA API (e.g., `https://api.us1.fga.dev/`).

It is strongly recommended to use environment variables for all these sensitive values.

```php
<?php
use OpenFGA\Client;
use OpenFGA\Enum\Authentication;

// Recommended: Load all credentials from environment variables
$clientId = $_ENV['OPENFGA_CLIENT_ID'] ?? null;
$clientSecret = $_ENV['OPENFGA_CLIENT_SECRET'] ?? null;
$issuerUrl = $_ENV['OPENFGA_ISSUER_URL'] ?? null; // e.g., https://your-auth0-domain/oauth/token
$audience = $_ENV['OPENFGA_AUDIENCE'] ?? null;   // e.g., https://api.us1.fga.dev/

if (!$clientId || !$clientSecret || !$issuerUrl || !$audience) {
    die("Missing required environment variables for Client Credentials authentication.\n");
}

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080', // Your OpenFGA server URL
    authentication: Authentication::CLIENT_CREDENTIALS,
    clientId: $clientId,
    clientSecret: $clientSecret,
    issuer: $issuerUrl,
    audience: $audience
);

echo "Client configured for Client Credentials Authentication.\n";
// The SDK will automatically handle fetching and refreshing the access token.
// $client is now ready to make authenticated requests.
?>
```

**Suggested Environment Variables:**
*   `OPENFGA_CLIENT_ID` or `FGA_CLIENT_ID`
*   `OPENFGA_CLIENT_SECRET` or `FGA_CLIENT_SECRET`
*   `OPENFGA_ISSUER_URL` or `FGA_ISSUER_URL` (or simply `OPENFGA_ISSUER`)
*   `OPENFGA_AUDIENCE` or `FGA_AUDIENCE`

**Use Cases:**
*   Services like Auth0 FGA, which typically use this method.
*   OpenFGA deployments that are secured using an external OAuth 2.0 identity provider.
*   Environments where short-lived, dynamically obtained tokens are preferred for enhanced security.

## Environment Variables Best Practices

Using environment variables for sensitive credentials (like API tokens, client secrets) and configuration (like URLs, client IDs) is a security best practice. It offers several advantages:

*   **Security:** Keeps sensitive data out of your codebase and version control.
*   **Flexibility:** Allows different configurations for different environments (development, staging, production) without code changes.
*   **Standardization:** Aligns with common deployment practices (e.g., Docker, Kubernetes, PaaS).

Consider using a library (like `vlucas/phpdotenv`) to load environment variables from a `.env` file during development, while relying on your hosting environment's native support for environment variables in production.

**Consistent Naming:**
Using a consistent prefix for your OpenFGA-related environment variables, such as `OPENFGA_` or `FGA_`, can help organize your configuration. For example:
*   `FGA_API_URL`
*   `FGA_STORE_ID`
*   `FGA_MODEL_ID`
*   `FGA_API_TOKEN`
*   `FGA_CLIENT_ID`
*   `FGA_CLIENT_SECRET`

## Next Steps

Once your client is successfully configured for authentication and can connect to your OpenFGA server, you are ready to:

*   **[Manage Stores](Stores.md)**: Create and manage isolated environments for your authorization data.
*   Proceed with other SDK operations like defining [Authorization Models](AuthorizationModels.md), writing [Relationship Tuples](RelationshipTuples.md), and performing [Queries](Queries.md).

Refer to the specific documentation for your OpenFGA server deployment (e.g., Auth0 FGA dashboard, OpenFGA Helm chart values) to determine which authentication method is supported and how to obtain the necessary credentials.
```
