# Assertions

Read and write assertions for testing.

```php
use OpenFGA\Client;

$client = new Client(url: 'http://localhost:8080');

$assertions = $client->readAssertions('store-id', 'model-id');
$client->writeAssertions('store-id', 'model-id', $assertions);
```
