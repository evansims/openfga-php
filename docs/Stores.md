# Stores

Stores are isolated environments that contain their own authorization model and data, allowing you to manage permissions separately for different applications, tenants, or environments.

> [!TIP]
> These examples assume you're continuing from [Getting Started](/docs/GettingStarted.md), where we set up our common imports, and initialized the SDK as `$client`.

## Creating Stores

```php
use OpenFGA\Responses\CreateStoreResponseInterface;

$result = $client->createStore(name: 'my-store');

echo fold(
    $result,
    fn(CreateStoreResponseInterface $response) => "Store created: {$response->getId()}",
    fn(Throwable $err) => "Store creation failed: {$err->getMessage()}"
);

failure($result, fn(Throwable $error) => {
    // Store creation failed. Log or gracefully handle the error.
    throw $error;
});
```

## Listing Stores

```php
use OpenFGA\Responses\ListStoresResponseInterface;

$result = $client->listStores();

success($result, fn(ListStoresResponseInterface $response) => {
    foreach ($response->getStores() as $store) {
        echo "Store: {$store->getId()}\n";
    }
});
```

## Getting a Store

```php
use OpenFGA\Responses\GetStoreResponseInterface;

($client->getStore(store: 'my-store'))->onSuccess(fn(GetStoreResponseInterface $response) => {
    echo "Store ID: {$response->getId()}\n";
    echo "Store Name: {$response->getName()}\n";
    echo "Store Created At: {$response->getCreatedAt()->format('Y-m-d H:i:s')}\n";
    echo "Store Updated At: {$response->getUpdatedAt()->format('Y-m-d H:i:s')}\n";
});
```

## Deleting a Store

```php
success($store, fn(CreateStoreResponseInterface $store) => {
    // Delete the store we created with our `createStore()` call above.
    $client->deleteStore(store: $store->getId());
});
```

## Next Steps

1. **Next, you'll need to [create an authorization model](/docs/AuthorizationModels.md).**

2. **Then, you'll need to [create some relationship tuples](/docs/RelationshipTuples.md).**

- With relationship tuples in place, you can then [query](/docs/Queries.md) for specific users' access to certain resources, based on your authorization model.

- It's also a good idea to create [assertions](/docs/Assertions.md) to validate that your access rules behave as expected and catch mistakes early.
