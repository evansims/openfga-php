# Authentication

The client supports token and client credentials authentication. The URL is the only required option when authentication is not needed.

## Token authentication

```php
use OpenFGA\Authentication\AuthenticationMode;
use OpenFGA\Client;

$client = new Client(
    url: 'http://localhost:8080',
    authenticationMode: AuthenticationMode::TOKEN,
    token: 'my-token',
);
```

## Client credentials

```php
$client = new Client(
    url: 'http://localhost:8080',
    authenticationMode: AuthenticationMode::CLIENT_CREDENTIALS,
    clientId: 'client-id',
    clientSecret: 'client-secret',
    issuer: 'https://issuer.example',
    audience: 'https://api.example',
);
```
