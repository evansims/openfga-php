# Relationship tuples

Write, read and track relationship tuples.

```php
use OpenFGA\{Client, Authentication};
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\TupleKey;

$client = new Client(
    url: 'http://localhost:8080',
    authentication: Authentication::NONE,
);

$tuples = new TupleKeys([
    new TupleKey(user: 'user:anne', relation: 'writer', object: 'doc:1'),
]);

$client->writeTuples(store: 'store-id', writes: $tuples, model: 'model-id');

$result = $client->readTuples('store-id', new TupleKey(user: 'user:anne', relation: 'writer', object: 'doc:1'));

$changes = $client->listTupleChanges('store-id');
```
