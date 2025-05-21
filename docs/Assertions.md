# Assertions

Read and write assertions for testing.

```php
use OpenFGA\{Client, Authentication};

$client = new Client(
    url: 'http://localhost:8080',
    authentication: Authentication::NONE,
);

$assertions = $client->readAssertions('store-id', 'model-id');
$client->writeAssertions('store-id', 'model-id', $assertions);
```
