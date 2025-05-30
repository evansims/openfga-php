# Authorization Models

Authorization models are your permission blueprint. They define what types of things exist in your system and how they relate to each other. Think database schema, but for permissions.

## Building your first model

Let's jump right into building a document sharing system. Here's what we want:

- Users can own, edit, or view documents
- Owners can do everything
- Editors can edit and view
- Viewers can only view

```fsharp
model
  schema 1.1

type user

type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define viewer: [user] or editor
```

That's it. The `or` keyword creates inheritance - owners automatically become editors, and editors automatically become viewers.

## Creating your model

Transform your DSL into a model object and create it on the server:

```php
use OpenFGA\Client;

$client = new Client(url: 'http://localhost:8080');

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

// Transform DSL to model object
$model = $client->dsl($dsl)->unwrap();

// Create on server
$response = $client->createAuthorizationModel(
    store: $storeId,
    typeDefinitions: $model->getTypeDefinitions(),
    conditions: $model->getConditions()
)->unwrap();

$modelId = $response->getId();
```

Save that `$modelId` - you'll need it for everything else.

## Common patterns

### Direct assignment

The simplest relationship - a user directly has a role:

```fsharp
type document
  relations
    define owner: [user]
```

This lets you write tuples like `user:alice owner document:readme`.

### Computed relations

Relations that inherit from other relations:

```fsharp
type document
  relations
    define owner: [user]
    define editor: owner  // All owners are editors
    define viewer: editor // All editors are viewers
```

### Union relations

Multiple ways to get the same permission:

```fsharp
type document
  relations
    define owner: [user]
    define editor: [user] or owner     // Direct editors OR owners
    define viewer: [user] or editor    // Direct viewers OR editors
```

### Hierarchical permissions

Inherit permissions from parent objects:

```fsharp
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

```fsharp
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

```fsharp
type document
  relations
    define viewer: [user with valid_ip]
    define editor: [user with business_hours]
```

Define conditions when creating your model:

```php
use OpenFGA\Models\{Condition, ConditionMetadata, ConditionParameter};
use OpenFGA\Models\Collections\{Conditions, ConditionParameters};

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

$response = $client->createAuthorizationModel(
    store: $storeId,
    typeDefinitions: $model->getTypeDefinitions(),
    conditions: $conditions
)->unwrap();
```

## Using models in your application

### Set the active model

```php
$client->setModel($modelId);
```

### Check permissions

```php
$canView = $client->check(
    user: 'user:alice',
    relation: 'viewer',
    object: 'document:readme'
)->unwrap()->getAllowed();
```

### List user's objects

```php
$documents = $client->listObjects(
    user: 'user:alice',
    relation: 'viewer',
    type: 'document'
)->unwrap()->getObjects();
```

## Advanced patterns

### Multi-tenant systems

Each tenant has their own workspace:

```fsharp
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

```fsharp
type document
  relations
    define owner: [user]
    define editor: [user] or owner
    define approver: [user]
    define can_publish: approver and owner
    define viewer: [user] or can_publish
```

### Time-based access

```fsharp
type document
  relations
    define owner: [user]
    define viewer: [user with during_work_hours] or owner
```

### Nested resources

Permissions flow down through resource hierarchies:

```fsharp
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

```php
$models = $client->listAuthorizationModels(
    store: $storeId,
    pageSize: 10
)->unwrap()->getModels();

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

### Version your models

Always create new models instead of modifying existing ones. Keep the old model ID for backward compatibility:

```php
// Deploy new model
$newModelId = $client->createAuthorizationModel(/*...*/)
    ->unwrap()->getId();

// Gradually migrate tuples to new model
// Switch applications to use $newModelId
```

## What's next

Now that you have a model:

1. **[Write relationship tuples](Tuples.md)** - Connect users to objects
2. **[Perform queries](Queries.md)** - Check permissions and list objects  
3. **[Test with assertions](Assertions.md)** - Verify your model works correctly

Your authorization model is the foundation everything else builds on. Take time to design it well.