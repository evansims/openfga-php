The OpenFGA PHP SDK provides a collection of helper functions that dramatically simplify common authorization operations. These helpers reduce boilerplate code and make your authorization logic more readable and maintainable.

## Prerequisites

All examples in this guide assume the following setup:

```php
<?php

declare(strict_types=1);

use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080',
);
```

## Context helper

The `context()` helper sets ambient values that other helpers can use implicitly, eliminating the need to pass client, store, and model parameters to every function call. This is especially powerful when combined with other helpers.

### Basic usage

```php
use function OpenFGA\{context, allowed, write, tuple};

$result = context(function() {
    // All helpers within this context can omit client/store/model parameters
    $canView = allowed(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget'
    );
    
    if (!$canView) {
        write(tuple('user:anne', 'viewer', 'document:budget'));
    }
    
    return $canView;
}, client: $client, store: $storeId, model: $modelId);
```

### Nested contexts with inheritance

Child contexts automatically inherit values from their parent context unless explicitly overridden:

```php
use function OpenFGA\{context, users, filter};

context(function() {
    // Uses outer context's client and store
    $viewers = users('document:public', 'viewer', filter('user'));
    
    context(function() {
        // Inherits client/store from parent, but uses different model
        $editors = users('document:private', 'editor', filter('user'));
    }, model: $privateModelId);
    
}, client: $client, store: $storeId, model: $publicModelId);
```

### Partial context overrides

```php
use function OpenFGA\{context, allowed, tuple};

context(function() {
    // Set base client and store for user operations
    
    context(function() {
        // Override just the store for admin operations
        $isAdmin = allowed(tuple: tuple('user:anne', 'admin', 'system:settings'));
    }, store: $adminStoreId);
    
}, client: $client, store: $userStoreId, model: $modelId);
```

## Model helpers

### Creating tuples

The `tuple()` helper simplifies creating relationship tuples:

```php
use function OpenFGA\tuple;

$tuple = tuple(
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:budget',
);
```

With conditions:

```php
use OpenFGA\Models\Condition;
use function OpenFGA\tuple;

$tuple = tuple(
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:budget',
    condition: new Condition(name: 'in_office_hours', expression: /* ... */),
);
```

### Creating tuple collections

The `tuples()` helper creates collections for batch operations:

```php
use function OpenFGA\{tuple, tuples};

$tupleCollection = tuples(
    tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
    tuple(
        user: 'user:bob',
        relation: 'editor',
        object: 'document:budget',
    ),
    tuple(
        user: 'user:charlie',
        relation: 'owner',
        object: 'document:budget',
    ),
);
```

### Creating batch check items

The `check()` helper creates BatchCheckItem instances for batch authorization checks:

```php
use function OpenFGA\{check, tuple};

$checkItem = check(
    tuple: tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
    // correlation: 'anne-budget-check', // Optional
);
```

If no correlation ID is provided, one is automatically generated based on the tuple key.

### Creating stores

The `store()` helper creates a store and returns its ID directly:

```php
use function OpenFGA\store;

$storeId = store(
    name: 'my-application-store',
    client: $client,
);
```

### Creating authorization models from DSL

The `dsl()` helper transforms DSL strings into authorization models:

```yaml
model
schema 1.1
type user
type document
relations
    define viewer: [user]
    define editor: [user]
    define owner: [user]
```

```php
use function OpenFGA\dsl;

$model = dsl(
    dsl: file_get_contents('path/to/dsl.fga')
    client: $client,
);
```

### Creating models in a store

The `model()` helper creates an authorization model and returns its ID:

```php
use function OpenFGA\model;

$modelId = model(
    model: $model,
    client: $client,
    store: $storeId,
);
```

## Request helpers

### Granting permissions

The `grant()` helper provides an intuitive way to grant permissions. It's functionally equivalent to `write()` but uses clearer terminology for permission management:

```php
use function OpenFGA\{tuple, grant};

// Grant a single permission
grant(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
);

// Grant multiple permissions at once
use function OpenFGA\{tuples};

grant(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        tuple(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:budget',
        ),
        tuple(
            user: 'user:anne',
            relation: 'editor',
            object: 'document:forecast',
        ),
    ),
);

// Grant permissions based on business logic
if ($user->hasSubscription()) {
    grant(tuple($user->getId(), 'premium_user', 'feature:advanced_analytics'));
}
```

### Writing tuples

The `write()` helper provides the simplest way to write a tuple:

```php
use function OpenFGA\{tuple, write};

write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
);
```

The helper also supports writing multiple tuples at once:

```php
use function OpenFGA\{tuple, tuples, write};

write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        tuple(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:budget',
        ),
        tuple(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:forecast',
        ),
    ),
);
```

### Revoking permissions

The `revoke()` helper provides an intuitive way to revoke permissions. It's functionally equivalent to `delete()` but uses clearer terminology for permission management:

```php
use function OpenFGA\{tuple, revoke};

// Revoke a single permission
revoke(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:anne',
        relation: 'editor',
        object: 'document:budget',
    ),
);

// Revoke multiple permissions at once
use function OpenFGA\{tuples};

revoke(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        tuple(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:budget',
        ),
        tuple(
            user: 'user:anne',
            relation: 'editor',
            object: 'document:forecast',
        ),
    ),
);

// Revoke permissions based on business logic
if ($user->subscriptionExpired()) {
    revoke(tuple($user->getId(), 'premium_user', 'feature:advanced_analytics'));
}
```

### Deleting tuples

The `delete()` helper simplifies tuple deletion:

```php
use function OpenFGA\{tuple, delete};

delete(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
);
```

### Checking permissions

The `allowed()` helper returns a boolean directly with guaranteed error-safe behavior. It will return true only when explicitly allowed, and false when denied or any error occurs.

```php
use function OpenFGA\{tuple, allowed};

if (allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:budget',
)) {
    // User has access
}
```

The helper supports a number of options:

```php
use OpenFGA\Models\Enums\Consistency;
use function OpenFGA\{tuple, allowed};

if (allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple(
        user: 'user:anne',
        relation: 'viewer',
        object: 'document:budget',
    ),
    context: (object)['time' => '2024-01-15T10:00:00Z'],
    contextualTuples: tuples(
        tuple(
            user: 'user:anne',
            relation: 'member',
            object: 'team:engineering',
        )
    ),
    consistency: Consistency::HIGHER_CONSISTENCY
)) {
    // User has access
}
```

### Finding accessible objects

The `objects()` helper simplifies finding all objects a user has access to with guaranteed error-safe behavior. It returns an empty array on any error.

```php
use function OpenFGA\objects;

$documents = objects(
    type: 'document',
    relation: 'viewer',
    user: 'user:anne',
    client: $client,
    store: $storeId,
    model: $modelId,
);

// Returns array of accessible documents or [] on any error
```

The helper supports a number of options:

```php
use function OpenFGA\{objects, tuple, tuples};

$documents = objects(
    type: 'document',
    relation: 'viewer',
    user: 'user:anne',
    client: $client,
    store: $storeId,
    model: $modelId,
    context: (object)['department' => 'engineering'],
    contextualTuples: tuples(
        tuple(
            user: 'user:anne',
            relation: 'member',
            object: 'team:engineering',
        )
    ),
    consistency: Consistency::HIGHER_CONSISTENCY
);
```

### Listing authorization models

The `models()` helper simplifies retrieving authorization models with automatic pagination:

```php
use function OpenFGA\models;

$models = models(
    client: $client,
    store: $storeId,
);

foreach ($models as $model) {
    echo "Model: {$model->getId()}\n";
}
```

### Batch write operations

The `writes()` helper handles large tuple operations with automatic chunking:

```php
use function OpenFGA\{tuple, tuples, writes};

$result = writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:1',
        ),
        tuple(
            user: 'user:bob',
            relation: 'viewer',
            object: 'document:2',
        ),
        // ...
    ),
    maxParallelRequests: 10,
    maxTuplesPerChunk: 100,
    maxRetries: 3,
    stopOnFirstError: false
);

echo "Success rate: " . ($result->getSuccessRate() * 100) . "%\n";
```

### Batch authorization checks

The `checks()` helper simplifies performing multiple authorization checks in a single request:

```php
use function OpenFGA\{checks, check};

$results = checks(
    client: $client,
    store: $storeId,
    model: $modelId,
    check(user: 'user:anne', relation: 'viewer', object: 'document:budget'),
    check(user: 'user:bob', relation: 'editor', object: 'document:budget'),
    check(user: 'user:charlie', relation: 'owner', object: 'document:budget')
);

foreach ($results as $correlationId => $allowed) {
    echo "$correlationId: " . ($allowed ? 'allowed' : 'denied') . "\n";
}
```

## Result helpers

### Creating results

```php
use function OpenFGA\{ok, err};

// Create a Success
$success = ok('Operation completed');

// Create a Failure
$failure = err(new Exception('Operation failed'));
```

### Working with results

The `result()` helper provides unified handling:

```php
use function OpenFGA\result;

// Execute a closure safely
$result = result(function () {
    // Some operation that might throw
    return performRiskyOperation();
});

// Unwrap a Result
$value = result($someResult);
```

### Handling success and failure

**Helper usage:**

```php
use function OpenFGA\{success, failure, unwrap};

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

## Reading tuples

The `read()` helper provides a simplified way to read tuples with automatic pagination:

```php
use function OpenFGA\{read, tuple};

// Read all tuples in a store
$allTuples = read(
    client: $client,
    store: $storeId
);

// Read tuples filtered by user
$userTuples = read(
    client: $client,
    store: $storeId,
    tuple: tuple('user:anne', '', '')  // Empty relation and object act as wildcards
);

// Read with custom page size and consistency
use OpenFGA\Models\Enums\Consistency;

$tuples = read(
    client: $client,
    store: $storeId,
    pageSize: 100,
    consistency: Consistency::HigherConsistency
);

// Using with context
context(function() {
    $allTuples = read();  // Uses ambient client/store from context
    foreach ($allTuples as $tuple) {
        echo "{$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
    }
}, client: $client, store: $storeId);
```

## Listing tuple changes

The `changes()` helper retrieves all tuple changes with automatic pagination:

```php
use function OpenFGA\changes;

// Get all changes in a store
$allChanges = changes(
    client: $client,
    store: $storeId
);

foreach ($allChanges as $change) {
    echo "Change: {$change->getOperation()->value} at {$change->getTimestamp()->format('Y-m-d H:i:s')}\n";
}

// Filter changes by object type
$documentChanges = changes(
    client: $client,
    store: $storeId,
    type: 'document'
);

// Get recent changes since yesterday
$recentChanges = changes(
    client: $client,
    store: $storeId,
    startTime: new DateTimeImmutable('-1 day')
);

// Using with context and custom page size
context(function() {
    $changes = changes(pageSize: 100);
    echo "Total changes: " . count($changes) . "\n";
}, client: $client, store: $storeId);
```

## Finding users with access

The `users()` helper lists all users who have a specific relationship with an object:

```php
use function OpenFGA\{users, filter, filters};

// Find all users who can view a document
$viewers = users(
    object: 'document:budget',
    relation: 'viewer',
    filters: filter('user'),
    client: $client,
    store: $storeId,
    model: $modelId
);
// Returns: ['user:anne', 'user:bob', 'user:charlie']

// Find both users and groups with edit access
$editors = users(
    object: 'document:budget',
    relation: 'editor',
    filters: filters(
        filter('user'),
        filter('group')
    ),
    client: $client,
    store: $storeId,
    model: $modelId
);
// Returns: ['user:anne', 'group:engineering', 'user:david']

// Filter by relation type
$teamMembers = users(
    object: 'document:technical-spec',
    relation: 'editor',
    filters: filter('team', 'member'),  // Only team members
    client: $client,
    store: $storeId,
    model: $modelId
);

// Using with context and contextual tuples
context(function() {
    use function OpenFGA\{tuple, tuples};
    
    $editors = users(
        object: 'document:technical-spec',
        relation: 'editor',
        filters: filter('user'),
        contextualTuples: tuples(
            tuple('user:anne', 'member', 'team:engineering')
        )
    );
}, client: $client, store: $storeId, model: $modelId);
```

## User type filters

The `filter()` and `filters()` helpers create user type filters for queries:

```php
use function OpenFGA\{filter, filters};

// Single user type filter
$userFilter = filter('user');

// Filter with relation
$groupMemberFilter = filter('group', 'member');

// Multiple filters collection
$mixedFilters = filters(
    filter('user'),
    filter('group'),
    filter('service_account'),
    filter('team', 'member')
);
```

## Language helpers

The `lang()` and `trans()` helpers simplify working with internationalization:

```php
use OpenFGA\Language;
use OpenFGA\Messages;
use function OpenFGA\{lang, trans};

// Get language enum by locale
$german = lang('de');           // Returns Language::German
$portuguese = lang('pt_BR');    // Returns Language::PortugueseBrazilian
$default = lang();              // Returns Language::English (default)

// Translate messages
$message = trans(Messages::NO_LAST_REQUEST_FOUND);

// Translate with parameters
$error = trans(
    Messages::NETWORK_ERROR,
    ['message' => 'Connection timeout']
);

// Translate with specific language
$germanError = trans(
    Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED,
    [],
    Language::German
);

// Using in client configuration
$client = new Client(
    url: $url,
    language: lang('de')
);
```

## Next steps

- Explore the Tuples Guide for more details on relationship tuples
- Learn about Batch Operations for handling large datasets
- Understand Result Patterns for robust error handling
- See Integration Examples for real-world usage patterns
