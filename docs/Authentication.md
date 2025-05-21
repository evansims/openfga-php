# Authentication

The client supports token and client credentials authentication. The URL is the only required option when authentication is not needed.

## Token authentication

```php
use OpenFGA\{Client, Authentication};

$client = new Client(
    url: 'http://localhost:8080',
    authentication: Authentication::TOKEN,
    token: getenv('OPENFGA_TOKEN'),
);
```

## Client credentials

```php
use OpenFGA\{Client, Authentication};

$client = new Client(
    url: 'http://localhost:8080',
    authentication: Authentication::CLIENT_CREDENTIALS,
    clientId: getenv('OPENFGA_CLIENT_ID'),
    clientSecret: getenv('OPENFGA_CLIENT_SECRET'),
    issuer: getenv('OPENFGA_ISSUER'),
    audience: getenv('OPENFGA_AUDIENCE'),
);
```
