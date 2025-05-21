# Usage

Basic setup and configuration.

```php
use OpenFGA\Authentication\AuthenticationMode;
use OpenFGA\Client;

$client = new Client(
    url: 'http://localhost:8080',
    authenticationMode: AuthenticationMode::NONE,
);
```

See the other documents in this folder for API details.
