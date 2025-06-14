Authorization models are your permission blueprint. They define what types of things exist in your system and how they relate to each other. Think database schema, but for permissions.

## Prerequisites

The examples in this guide assume you have the following setup:

```php
use OpenFGA\Client;
use OpenFGA\Models\{Condition, ConditionMetadata, ConditionParameter};
use OpenFGA\Models\Collections\{Conditions, ConditionParameters};

$client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');

$storeId = 'your-store-id'; // From creating a store
$modelId = 'your-model-id'; // From creating an authorization model
```

## Building your first model

Let's jump right into building a document sharing system. Here's what we want:

- Users can own, edit, or view documents
- Owners can do everything
- Editors can edit and view
- Viewers can only view

We'll define this model using OpenFGA's [DSL](https://openfga.dev/docs/configuration-language) format:

```yaml
model
  schema 1.1

type user

type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define viewer: [user] or editor
```

The `or` keyword creates inheritance - owners automatically become editors, and editors automatically become viewers.

## Creating your model

Use the SDK's `dsl` [helper](../Features/Helpers.md) to create a model, then use the `model` [helper](../Features/Helpers.md) to commit the model to the OpenFGA server:

```php
use function OpenFGA\{dsl, model};

$dsl = <<<DSL
model
  schema 1.1

type user

type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define viewer: [user] or editor
DSL;

// Transform DSL to model object using the dsl() helper
$model = dsl(
  client: $client,
  dsl: $dsl,
);

// Commit the model to the server using the model() helper
$modelId = model(
  client: $client,
  store $storeId,
  model: $model,
);
```

Save that returned `$modelId` — you'll need it for future API calls.

## Common patterns

### Direct assignment

The simplest relationship - a user directly has a role:

```yaml
type document
  relations
    define owner: [user]
```

This lets you write tuples like `user:alice owner document:readme`.

### Computed relations

Relations that inherit from other relations:

```yaml
type document
  relations
    define owner: [user]
    define editor: owner  // All owners are editors
    define viewer: editor // All editors are viewers
```

### Union relations

Multiple ways to get the same permission:

```yaml
type document
  relations
    define owner: [user]
    define editor: [user] or owner     // Direct editors OR owners
    define viewer: [user] or editor    // Direct viewers OR editors
```

### Hierarchical permissions

Inherit permissions from parent objects:

```yaml
type folder
  relations
    define owner: [user]
    define viewer: [user] or owner

type document
  relations
    define parent: [folder]
    define owner: [user]
    define viewer: [user] or owner or viewer from parent
```

Now documents inherit viewer permissions from their parent folder.

### Group membership

Users belong to groups, groups have permissions:

```yaml
type user

type group
  relations
    define member: [user]

type document
  relations
    define owner: [user, group#member]
    define viewer: [user, group#member] or owner
```

The `group#member` syntax means "users who are members of the group".

## Working with conditions

Add context-aware permissions using conditions:

```yaml
type document
  relations
    define viewer: [user with valid_ip]
    define editor: [user with business_hours]
```

Define conditions when creating your model:

```php
$conditions = new Conditions([
    new Condition(
        name: 'valid_ip',
        expression: 'ip_address in allowed_ips',
        parameters: new ConditionParameters([
            new ConditionParameter(
                name: 'allowed_ips',
                typeName: 'list'
            )
        ])
    )
]);

$modelId = $client->createAuthorizationModel(
    store: $storeId,
    typeDefinitions: $model->getTypeDefinitions(),
    conditions: $conditions
)
  ->rethrow()
  ->unwrap(fn($model) => $model->getId());
```

## Using models in your application

### Check permissions

The `allowed` [helper](../Features/Helpers.md#checking-permissions) provides a convenient shorthand for checking permissions, and returns a boolean:

```php
use function OpenFGA\{allowed, tuple};

$canView = allowed(
  client: $client,
  store: $storeId,
  model: $modelId,
  tuple: tuple(
    user: 'user:alice',
    relation: 'viewer',
    object: 'document:readme',
  ),
);

if ($canView) {
  echo 'Alice can view the readme';
}
```

If you need greater control over the operation, use the Client `check` method directly:

```php
use function OpenFGA\{success, failure, tuple};

$canView = false;
$result = $client->check(
    store: $storeId,
    model: $modelId,
    tuple: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:readme',
    )
);

success($result, function ($value) use (&$canView) {
    $canView = $value->getAllowed();
});

failure($result, function ($error) {
    echo "Error: " . $error->getMessage() . "\n";
});

if ($canView) {
    echo 'Alice can view the readme';
}
```

### List user's objects

The `objects` [helper](../Features/Helpers.md#listing-objects) provides a convenient shorthand for listing user's objects, and returns an array of object identifiers:

```php
use function OpenFGA\objects;

$objects = objects(
    client: $client,
    store: $storeId,
    model: $modelId,
    type: 'document',
    relation: 'viewer',
    user: 'user:alice',
);

echo "Alice can view the following documents:\n";

foreach ($objects as $object) {
    echo $object . "\n";
}
```

If you need greater control over the operation, use the Client `streamedListObjects` or `listObjects` methods directly:

```php
use function OpenFGA\{success, failure};

$objects = [];
$result = $client->streamedListObjects(
    store: $storeId,
    model: $modelId,
    type: 'document',
    relation: 'viewer',
    user: 'user:alice',
);

success($result, function ($value) use (&$objects) {
    $objects = $value->getObjects();
});

failure($result, function ($error) {
    echo "Error: " . $error->getMessage() . "\n";
});

echo "Alice can view the following documents:\n";

foreach ($objects as $object) {
    echo $object . "\n";
}
```

## Advanced patterns

### Multi-tenant systems

Each tenant has their own workspace:

```yaml
type user

type tenant
  relations
    define member: [user]
    define admin: [user]

type document
  relations
    define tenant: [tenant]
    define owner: [user] and member from tenant
    define viewer: [user] and member from tenant
```

The `and` keyword requires both conditions - users must be both assigned the role AND be members of the tenant.

### Approval workflows

Documents need approval before publishing:

```yaml
type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define approver: [user]
    define can_publish: approver and owner
    define viewer: [user] or can_publish
```

### Time-based access

```yaml
type document
  relations
    define owner: [user]
    define viewer: [user with during_work_hours] or owner
```

### Nested resources

Permissions flow down through resource hierarchies:

```yaml
type organization
  relations
    define admin: [user]
    define member: [user] or admin

type project
  relations
    define organization: [organization]
    define admin: [user] or admin from organization
    define member: [user] or member from organization

type document
  relations
    define project: [project]
    define editor: [user] or admin from project
    define viewer: [user] or member from project
```

## Managing models

### List all models

The `models` helper provides a convenient, self-paginated method for retrieving all the authorization models in a store:

```php
use function OpenFGA\models;

$models = models(client: $client, store: $storeId);

foreach ($models as $model) {
    echo "Model ID: " . $model->getId() . "\n";
}
```

Alternatively you can call the Client `listAuthorizationModels` method directly:

```php
$models = [];
$continuationToken = null;
do {
    $response = $client->listAuthorizationModels(
        store: $storeId,
        pageSize: 10,
        continuationToken: $continuationToken,
    )->unwrap();

    foreach ($response->getModels() as $model) {
        $models[] = $model;
    }

    $continuationToken = $response->getContinuationToken();
} while (null !== $continuationToken);

foreach ($models as $model) {
    echo "Model ID: " . $model->getId() . "\n";
}
```

### Get a specific model

```php
$model = $client->getAuthorizationModel(
    store: $storeId,
    model: $modelId
)->unwrap()->getModel();

// Convert back to DSL
echo $model->dsl();
```

## Troubleshooting Common Issues

### "My permissions aren't working as expected"

- Use [`expand()`](Queries.md#expand-relationships-debugging) to see the permission tree
- Check if you're using the correct model ID in your checks
- Verify your authorization model DSL syntax

### "Users have too many permissions"

- Check for unintended `or` relationships in your model
- Review inheritance patterns - owners might inherit editor/viewer permissions
- Use [assertions](Assertions.md) to test expected vs actual permissions

### "Users don't have enough permissions"

- Verify relationships are written correctly as [tuples](Tuples.md)
- Check if you're [querying](Queries.md) with the right object/relation names
- Use [`readTuples()`](Tuples.md#reading-existing-permissions) to see what permissions exist

## What's Next?

Once you've created your authorization model, it's crucial to test it thoroughly. The [Assertions guide](Assertions.md) shows you how to write comprehensive tests for your authorization models, ensuring they behave exactly as expected before deploying to production.
