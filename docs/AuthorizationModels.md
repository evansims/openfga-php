# Authorization Models

Authorization models define the structure of your access control system: what types of objects exist, what relationships (like viewer or owner) they support, and how access is granted. They’re the blueprint that tells the system how to interpret and enforce permissions.

> [!TIP]
> These examples assume you're continuing from [Getting Started](/docs/GettingStarted.md), where we set up our common imports, and initialized the SDK as `$client`.

> [!TIP]
> You'll need a store to work with authorization models. Learn how to create one in [Stores](/docs/Stores.md), and assign it's ID as `$store`.

## Transforming a DSL into an Authorization Model

```php
use OpenFGA\Models\AuthorizationModelInterface;

$dsl = <<<DSL
model
schema 1.1

type user

type domain
relations
    define member: [user]

type folder
relations
    define can_share: writer
    define owner: [user, domain#member] or owner from parent_folder
    define parent_folder: [folder]
    define viewer: [user, domain#member] or writer or viewer from parent_folder
    define writer: [user, domain#member] or owner or writer from parent_folder

type document
relations
    define can_share: writer
    define owner: [user, domain#member] or owner from parent_folder
    define parent_folder: [folder]
    define viewer: [user, domain#member] or writer or viewer from parent_folder
    define writer: [user, domain#member] or owner or writer from parent_folder
DSL;

$result = $client->dsl($dsl);

echo fold(
    $result,
    fn(AuthorizationModelInterface $model) => "DSL transformed successfully!",
    fn(Throwable $err) => "DSL transformation failed: {$err->getMessage()}"
);

failure($result, fn (Throwable $error): never => {
    // Handle transformation failure
    throw $error;
});

$model = unwrap($result);
```

## Creating an Authorization Model

```php
use OpenFGA\Responses\CreateAuthorizationModelResponseInterface;

($client->createAuthorizationModel(
    store: $store->getId(),
    typeDefinitions: $model->getTypeDefinitions(),
    conditions: $model->getConditions(),
))->onSuccess(fn(CreateAuthorizationModelResponseInterface $response) => {
    echo "Created Authorization Model ID: {$response->getModel()}\n";
});
```

## Listing Authorization Models

```php
use OpenFGA\Responses\ListAuthorizationModelsResponseInterface;

$continuationToken = null;

for ($i = 0; $i < 10; $i++) {
    $result = $client->listAuthorizationModels(
        store: $store->getId(),
        continuationToken: $continuationToken,
    );

    success($result, fn (ListAuthorizationModelsResponseInterface $response) => {
        foreach ($response->getModels() as $model) {
            echo "Authorization Model ID: {$model->getId()}\n";
        }

        $continuationToken = $response->getContinuationToken();
    });

    if (failure($result) || $continuationToken === null) {
        break;
    }
}
```

- The `continuationToken` parameter is only necessary when paginating results.
- The `pageSize` parameter controls the number of models returned per pagination request.

## Getting an Authorization Model

```php
use OpenFGA\Responses\GetAuthorizationModelResponseInterface;

$result = $client->getAuthorizationModel(
    store: $store->getId(),
    model: $model->getId()
);

success($result, fn(GetAuthorizationModelResponseInterface $response) => {
    $model = $response->getModel();

    echo "Authorization Model ID: {$model->getId()}\n\n";

    // Transform the AuthorizationModel into a DSL string with `AuthorizationModel::dsl()`.
    echo "Authorization Model DSL:\n\n{$model->dsl()}\n";
});
```

## Next Steps

1. **Next, you'll need to [create some relationship tuples](/docs/RelationshipTuples.md).**

- With relationship tuples in place, you can then [query](/docs/Queries.md) for specific users' access to certain resources, based on your authorization model.

- It's also a good idea to create [assertions](/docs/Assertions.md) to validate that your access rules behave as expected and catch mistakes early.
