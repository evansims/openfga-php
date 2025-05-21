# Stores

Create, list, fetch and delete stores.

```php
use OpenFGA\{Client, Authentication};

$client = new Client(
    url: 'http://localhost:8080',
    authentication: Authentication::NONE,
);

$response = $client->createStore('demo');
$storeId = $response->getId();

$stores = $client->listStores();
$store = $client->getStore($storeId);
$client->deleteStore($storeId);
```
