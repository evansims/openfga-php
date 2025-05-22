# Relationship Queries

> [!TIP]
> These examples assume you're continuing from [Getting Started](/docs/GettingStarted.md), where we set up our common imports, and initialized the SDK as `$client`.

> [!TIP]
> You'll need a store and an authorization model to work with relationship queries. Learn how to create a store in [Stores](/docs/Stores.md), and assign it's ID as `$store`. Then, learn how to create an authorization model in [Authorization Models](/docs/AuthorizationModels.md), and assign it's ID as `$model`.

## Checking for Access

Returns a simple answer to the question: "_Does_ this user have this access to this object?"

```php
use OpenFGA\Responses\CheckResponseInterface;
use OpenFGA\Models\TupleKey;

$tuple = new TupleKey(
    user: 'user:anne',
    relation: 'reader',
    object: 'document:roadmap',
)

$result = ($client->check(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: $tuple,
))->then(fn(CheckResponseInterface $response) => $response->getIsAllowed());

$allowed = $result->unwrap(false);
```

## Expanding Access Paths

Returns a detailed tree of all the possible access paths granting a specific relation on an object. "_How_ does this user have access to this object?"

```php
use OpenFGA\Responses\ExpandResponseInterface;

$tuple = new TupleKey(
    relation: 'reader',
    object: 'document:roadmap',
)

$result = $client->expand(
    store: $store->getId(),
    tupleKey: $tuple,
);

success($result, fn(ExpandResponseInterface $response) => {
    foreach ($response->getTuples() as $tuple) {
        echo "Tuple: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
    }
});
```

## Listing Users

Returns all users who have a specific type of access to a given object. "_Who_ has access to this object?"

```php
use OpenFGA\Responses\ListUsersResponseInterface;

$result = $client->listUsers(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: new TupleKey(relation: 'viewer', object: 'document:roadmap'),
);

success($result, fn(ListUsersResponseInterface $response) => {
    foreach ($response->getUsers() as $user) {
        echo "User: {$user->getUser()} {$user->getRelation()} {$user->getObject()}\n";
    }
});
```

## Listing Objects

Returns all the objects a specific user has a certain type of access to (like all documents they can view). "_What_ objects does this user have access to?"

```php
use OpenFGA\Responses\ListObjectsResponseInterface;

$result = $client->listObjects(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: new TupleKey(relation: 'viewer', object: 'document:roadmap'),
);

success($result, fn(ListObjectsResponseInterface $response) => {
    foreach ($response->getObjects() as $object) {
        echo "Object: {$object->getObject()} {$object->getRelation()} {$object->getUser()}\n";
    }
});
```

## Next Steps

- It's a good idea to create [assertions](/docs/Assertions.md) to validate that your access rules behave as expected and catch mistakes early.
