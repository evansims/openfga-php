# Authorization models

Create and retrieve authorization models.

```php
use OpenFGA\Client;
use OpenFGA\Models\Collections\{Conditions, TypeDefinitions};
use OpenFGA\Models\{Condition, TypeDefinition, Userset};

$client = new Client(url: 'http://localhost:8080');

$types = new TypeDefinitions([
    new TypeDefinition(
        type: 'document',
        relations: null,
    ),
]);

$conditions = new Conditions([
    new Condition(name: 'default', expression: 'true'),
]);

$model = $client->createAuthorizationModel(
    store: 'store-id',
    typeDefinitions: $types,
    conditions: $conditions,
);

$detail = $client->getAuthorizationModel('store-id', $model->getId());
```
