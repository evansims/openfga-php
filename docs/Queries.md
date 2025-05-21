# Relationship queries

Check and expand relationships or list matching objects and users.

```php
use OpenFGA\Client;
use OpenFGA\Models\TupleKey;

$client = new Client(url: 'http://localhost:8080');

$check = $client->check(
    store: 'store-id',
    authorizationModel: 'model-id',
    tupleKey: new TupleKey(user: 'user:anne', relation: 'reader', object: 'doc:1'),
);

$tree = $client->expand('store-id', new TupleKey(relation: 'viewer', object: 'doc:1'));
$objects = $client->listObjects('store-id', 'model-id', 'document', 'viewer', 'user:anne');
$users = $client->listUsers('store-id', 'model-id', 'doc:1', 'viewer');
```
