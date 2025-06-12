# Helper functions guide

The OpenFGA PHP SDK provides a collection of helper functions that dramatically simplify common authorization operations. These helpers reduce boilerplate code and make your authorization logic more readable and maintainable.

## Prerequisites

All examples in this guide assume the following setup:

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Models\{Store, AuthorizationModel, Condition, TupleKey};
use OpenFGA\Models\Collections\{TupleKeys, TypeDefinitions};
use OpenFGA\Models\Enums\{Consistency, SchemaVersion};
use OpenFGA\Results\{Success, Failure};

// Import helper functions
use function OpenFGA\{allowed, batch, delete, dsl, err, failure, model, ok, result, store, success, tuple, tuples, unwrap, write};

// Basic client setup
$client = new Client(url: 'http://localhost:8080');
```

## Model helpers

### Creating tuples

The `tuple()` helper simplifies creating relationship tuples:

**Helper usage:**
```php
$tuple = tuple('user:anne', 'viewer', 'document:budget');
```

**Standard long-form:**
```php
$tuple = new TupleKey('user:anne', 'viewer', 'document:budget');
```

With conditions:

**Helper usage:**
```php
$condition = new Condition(name: 'in_office_hours');
$tuple = tuple('user:anne', 'viewer', 'document:budget', $condition);
```

**Standard long-form:**
```php
$condition = new Condition(name: 'in_office_hours');
$tuple = new TupleKey('user:anne', 'viewer', 'document:budget', $condition);
```

### Creating tuple collections

The `tuples()` helper creates collections for batch operations:

**Helper usage:**
```php
$tupleCollection = tuples(
    tuple('user:anne', 'viewer', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'),
    tuple('user:charlie', 'owner', 'document:budget')
);
```

**Standard long-form:**
```php
$tupleCollection = new TupleKeys([
    new TupleKey('user:anne', 'viewer', 'document:budget'),
    new TupleKey('user:bob', 'editor', 'document:budget'),
    new TupleKey('user:charlie', 'owner', 'document:budget')
]);
```

### Creating stores

The `store()` helper creates a store and returns its ID directly:

**Helper usage:**
```php
$storeId = store($client, 'my-application');
```

**Standard long-form:**
```php
$response = $client->createStore(name: 'my-application')->unwrap();
$storeId = $response->getId();
```

### Creating authorization models from DSL

The `dsl()` helper parses DSL strings into authorization models:

**Helper usage:**
```php
$model = dsl($client, '
    model
      schema 1.1
    type user
    type document
      relations
        define viewer: [user]
        define editor: [user]
        define owner: [user]
');
```

**Standard long-form:**
```php
$model = $client->dsl('
    model
      schema 1.1
    type user
    type document
      relations
        define viewer: [user]
        define editor: [user]
        define owner: [user]
')->unwrap();
```

### Creating models in a store

The `model()` helper creates an authorization model and returns its ID:

**Helper usage:**
```php
$modelId = model($client, $storeId, $authModel);
```

**Standard long-form:**
```php
$response = $client->createAuthorizationModel(
    store: $storeId,
    typeDefinitions: $authModel->getTypeDefinitions(),
    conditions: $authModel->getConditions(),
    schemaVersion: $authModel->getSchemaVersion()
)->unwrap();
$modelId = $response->getModel();
```

## Request helpers

### Writing tuples

The `write()` helper provides the simplest way to write tuples:

**Helper usage (single tuple):**
```php
write($client, $storeId, $modelId, tuple('user:anne', 'viewer', 'document:budget'));
```

**Standard long-form (single tuple):**
```php
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys([new TupleKey('user:anne', 'viewer', 'document:budget')]),
    transactional: true
)->unwrap();
```

**Helper usage (multiple tuples):**
```php
write($client, $storeId, $modelId, tuples(
    tuple('user:anne', 'viewer', 'document:budget'),
    tuple('user:bob', 'editor', 'document:forecast')
));
```

**Standard long-form (multiple tuples):**
```php
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys([
        new TupleKey('user:anne', 'viewer', 'document:budget'),
        new TupleKey('user:bob', 'editor', 'document:forecast')
    ]),
    transactional: true
)->unwrap();
```

**Non-transactional mode:**
```php
// Helper - allows partial success
write($client, $storeId, $modelId, $tuples, transactional: false);

// Standard - allows partial success  
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: $tuples,
    transactional: false
)->unwrap();
```

### Deleting tuples

The `delete()` helper simplifies tuple deletion:

**Helper usage:**
```php
delete($client, $storeId, $modelId, tuple('user:anne', 'viewer', 'document:budget'));
```

**Standard long-form:**
```php
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    deletes: new TupleKeys([new TupleKey('user:anne', 'viewer', 'document:budget')]),
    transactional: true
)->unwrap();
```

### Checking permissions

The `allowed()` helper returns a boolean directly:

**Helper usage:**
```php
if (allowed($client, $storeId, $modelId, tuple('user:anne', 'viewer', 'document:budget'))) {
    // User has access
}
```

**Standard long-form:**
```php
$response = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget')
)->unwrap();

if ($response->getAllowed()) {
    // User has access
}
```

**With advanced options:**
```php
// Helper with all options
$hasAccess = allowed(
    $client, 
    $storeId, 
    $modelId, 
    tuple('user:anne', 'viewer', 'document:budget'),
    trace: true,
    context: (object)['time' => '2024-01-15T10:00:00Z'],
    contextualTuples: tuples(
        tuple('user:anne', 'member', 'team:engineering')
    ),
    consistency: Consistency::FULL
);

// Standard long-form
$response = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: new TupleKey('user:anne', 'viewer', 'document:budget'),
    trace: true,
    context: (object)['time' => '2024-01-15T10:00:00Z'],
    contextualTuples: new TupleKeys([
        new TupleKey('user:anne', 'member', 'team:engineering')
    ]),
    consistency: Consistency::FULL
)->unwrap();
$hasAccess = $response->getAllowed();
```

### Batch operations

The `batch()` helper handles large-scale tuple operations with automatic chunking:

**Helper usage:**
```php
$result = batch(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple('user:anne', 'viewer', 'document:1'),
        tuple('user:bob', 'viewer', 'document:2'),
        // ... hundreds more tuples
    ),
    maxParallelRequests: 10,
    maxTuplesPerChunk: 100,
    maxRetries: 3,
    stopOnFirstError: false
);

echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
```

**Standard long-form:**
```php
$result = $client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys([
        new TupleKey('user:anne', 'viewer', 'document:1'),
        new TupleKey('user:bob', 'viewer', 'document:2'),
        // ... hundreds more tuples
    ]),
    transactional: false,
    maxParallelRequests: 10,
    maxTuplesPerChunk: 100,
    maxRetries: 3,
    retryDelaySeconds: 1.0,
    stopOnFirstError: false
)->unwrap();

echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
```

## Result helpers

### Creating results

**Helper usage:**
```php
// Create a Success
$success = ok('Operation completed');

// Create a Failure
$failure = err(new Exception('Operation failed'));
```

**Standard long-form:**
```php
// Create a Success
$success = new Success('Operation completed');

// Create a Failure
$failure = new Failure(new Exception('Operation failed'));
```

### Working with results

The `result()` helper provides unified handling:

**Helper usage:**
```php
// Execute a closure safely
$result = result(function () {
    // Some operation that might throw
    return performRiskyOperation();
});

// Unwrap a Result
$value = result($someResult);
```

**Standard long-form:**
```php
// Execute a closure safely
try {
    $value = performRiskyOperation();
    $result = new Success($value);
} catch (Throwable $e) {
    $result = new Failure($e);
}

// Unwrap a Result
if ($someResult->failed()) {
    throw $someResult->err();
}
$value = $someResult->val();
```

### Handling success and failure

**Helper usage:**
```php
// Handle success
success($result, function ($value) {
    echo "Success: $value\n";
});

// Handle failure
failure($result, function ($error) {
    echo "Error: " . $error->getMessage() . "\n";
});

// Unwrap with fallback
$value = unwrap($result, fn() => 'default value');
```

**Standard long-form:**
```php
// Handle success
if ($result->succeeded()) {
    $value = $result->val();
    echo "Success: $value\n";
}

// Handle failure
if ($result->failed()) {
    $error = $result->err();
    echo "Error: " . $error->getMessage() . "\n";
}

// Unwrap with fallback
$value = $result->unwrap(fn() => 'default value');
```

## Complete example: Building an authorization system

Here's how helpers simplify building a complete authorization system:

**Using helpers:**
```php
// 1. Create a store
$storeId = store($client, 'document-sharing-app');

// 2. Define the authorization model
$authModel = dsl($client, '
    model
      schema 1.1
    
    type user
    
    type team
      relations
        define member: [user]
    
    type document
      relations
        define owner: [user]
        define editor: [user, team#member]
        define viewer: [user, team#member] or editor
');

// 3. Create the model in the store
$modelId = model($client, $storeId, $authModel);

// 4. Write relationships
write($client, $storeId, $modelId, tuples(
    // Direct permissions
    tuple('user:alice', 'owner', 'document:budget'),
    tuple('user:bob', 'editor', 'document:budget'),
    
    // Team membership
    tuple('user:charlie', 'member', 'team:finance'),
    tuple('user:david', 'member', 'team:finance'),
    
    // Team permissions
    tuple('team:finance#member', 'viewer', 'document:budget')
));

// 5. Check permissions
$users = ['alice', 'bob', 'charlie', 'david', 'eve'];
$relations = ['owner', 'editor', 'viewer'];

foreach ($users as $user) {
    foreach ($relations as $relation) {
        if (allowed($client, $storeId, $modelId, tuple("user:$user", $relation, 'document:budget'))) {
            echo "$user can $relation document:budget\n";
        }
    }
}
```

**Standard long-form equivalent:**
```php
// 1. Create a store
$storeResponse = $client->createStore(name: 'document-sharing-app')->unwrap();
$storeId = $storeResponse->getId();

// 2. Define the authorization model
$authModel = $client->dsl('
    model
      schema 1.1
    
    type user
    
    type team
      relations
        define member: [user]
    
    type document
      relations
        define owner: [user]
        define editor: [user, team#member]
        define viewer: [user, team#member] or editor
')->unwrap();

// 3. Create the model in the store
$modelResponse = $client->createAuthorizationModel(
    store: $storeId,
    typeDefinitions: $authModel->getTypeDefinitions(),
    conditions: $authModel->getConditions(),
    schemaVersion: $authModel->getSchemaVersion()
)->unwrap();
$modelId = $modelResponse->getModel();

// 4. Write relationships
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys([
        // Direct permissions
        new TupleKey('user:alice', 'owner', 'document:budget'),
        new TupleKey('user:bob', 'editor', 'document:budget'),
        
        // Team membership
        new TupleKey('user:charlie', 'member', 'team:finance'),
        new TupleKey('user:david', 'member', 'team:finance'),
        
        // Team permissions
        new TupleKey('team:finance#member', 'viewer', 'document:budget')
    ]),
    transactional: true
)->unwrap();

// 5. Check permissions
$users = ['alice', 'bob', 'charlie', 'david', 'eve'];
$relations = ['owner', 'editor', 'viewer'];

foreach ($users as $user) {
    foreach ($relations as $relation) {
        $checkResponse = $client->check(
            store: $storeId,
            model: $modelId,
            tupleKey: new TupleKey("user:$user", $relation, 'document:budget')
        )->unwrap();
        
        if ($checkResponse->getAllowed()) {
            echo "$user can $relation document:budget\n";
        }
    }
}
```

## When to use helpers vs standard methods

### Use helpers when:

- You want concise, readable code
- You're prototyping or exploring the API
- You're writing scripts or simple applications
- You prefer functional-style programming
- You want to reduce boilerplate in tests

### Use standard methods when:

- You need fine-grained control over error handling
- You're building complex error recovery logic
- You want to access all response metadata
- You're building abstractions on top of the SDK
- Your team prefers explicit object construction

## Benefits of using helpers

1. **Reduced Boilerplate**: Helpers eliminate repetitive code patterns
2. **Improved Readability**: Authorization logic becomes more declarative
3. **Faster Development**: Less typing means faster prototyping
4. **Fewer Errors**: Less code means fewer opportunities for mistakes
5. **Consistent Patterns**: Helpers enforce best practices by default

## Performance considerations

Helpers have minimal overhead - they're thin wrappers around the standard methods:

- `tuple()` and `tuples()` are simple constructors
- `write()`, `delete()`, and `allowed()` add one function call
- `batch()` delegates directly to the client's batch processing
- Result helpers add negligible overhead for error handling

For performance-critical paths, both approaches perform identically after PHP's optimizer runs.

## Next steps

- Explore the [Tuples Guide](Tuples.md) for more details on relationship tuples
- Learn about [Batch Operations](Concurrency.md#batch-operations) for handling large datasets
- Understand [Result Patterns](Results.md) for robust error handling
- See [Integration Examples](Integration.md) for real-world usage patterns