# Stores

Create, list, fetch and delete stores.

```php
use OpenFGA\Client;

$client = new Client(url: 'http://localhost:8080');

$response = $client->createStore('demo');
$storeId = $response->getId();

$all = $client->listStores();
$store = $client->getStore($storeId);
$client->deleteStore($storeId);
```
