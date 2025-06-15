**Queries are how your application enforces access control.** With an [authorization model](Models.md) and [relationship tuples](Tuples.md) in place, it's time to put them to use.

## Prerequisites

Before diving into the examples, make sure you have the necessary setup:

```php
use OpenFGA\Client;

// Initialize the client
$client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');

// Store and model identifiers from your configuration
$storeId = 'your-store-id';
$modelId = 'your-model-id';
```

## Queries

Queries let you ask OpenFGA about permissions. There are four types:

- **Check permissions**<br />
  "Can Alice edit this document?"
- **List accessible objects**<br />
  "What documents can Alice edit?"
- **Find users with permission**<br />
  "Who can edit this document?"
- **Expand relationships**<br />
  "How does Alice have edit access?"

### Check permissions

This is the query your application will make most often. Use the `allowed` [helper](../Features/Helpers.md) to check permissions and return a boolean value:

```php
use function OpenFGA\{allowed, tuple};

// Can Alice view the "roadmap" document?
$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:alice', 'viewer', 'document:roadmap')
);

echo match($canView) {
    true => "Alice CAN view the roadmap",
    false => "Alice CANNOT view the roadmap",
};
```

> The `allowed` helper wraps the Client `check` method, and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors and return `false` if the request fails.

<details>
<summary>Use the Client <code>check</code> method if you need more control over the operation…</summary>

```php
use OpenFGA\Responses\CheckResponseInterface;
use Throwable;
use function OpenFGA\tuple;

// *Can* Alice view the "roadmap" document?
$canView = $client->check(
    store: $storeId,
    model: $modelId,
    tupleKey: tuple('user:alice', 'viewer', 'document:roadmap')
)
    ->failure(fn(Throwable $error) => logError($error)) // ex: log error, send alert, etc.
    ->success(fn(CheckResponseInterface $response) => logSuccess($response)) // ex: log success, etc.
    ->then(fn(CheckResponseInterface $response) => $response?->getAllowed() ?? false) // ex: return boolean
    ->recover(fn(Throwable $error) => false) // ex: ignore errors; fallback to `false`
    ->unwrap();

echo match($canView) {
    true => "Alice CAN view the roadmap",
    false => "Alice CANNOT view the roadmap",
};
```

</details>

#### Check multiple permissions at once

Use the `checks` helper to check multiple permissions at once:

```php
use OpenFGA\{checks, check};

// *Can* Alice or Bob view the "roadmap" document?
$results = checks(
    client: $client,
    store: $storeId,
    model: $modelId,
    check(
      correlation: 'alice-check',
      user: 'user:alice',
      relation: 'viewer',
      object: 'document:roadmap',
    ),
    check(
      correlation: 'bob-check',
      tuple: tuple('user:bob', 'viewer', 'document:roadmap'),
    ),
);

// $results = [
//   'alice-check' => true,
//   'bob-check' => false,
// ]
```

> The `checks` helper wraps the Client `batchCheck` method, and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>batchCheck</code> method directly if you need more control over the operation…</summary>

```php
use OpenFGA\Models\BatchCheckItem;
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Responses\BatchCheckResponseInterface;
use Throwable;

// *Can* Alice or Bob view the "roadmap" document?

$checks = new BatchCheckItems(
  new BatchCheckItem(
    tupleKey: tuple('user:alice', 'viewer', 'document:roadmap'),
    correlationId: 'alice-check',
  ),
  new BatchCheckItem(
    tupleKey: tuple('user:bob', 'viewer', 'document:roadmap'),
    correlationId: 'bob-check',
  ),
);

$result = $client->batchCheck(
    store: $storeId,
    model: $modelId,
    checks: $checks
)
    ->failure(fn(Throwable $error) => logError($error)) // ex: log error, send alert, etc.
    ->success(fn(BatchCheckResponseInterface $response) => logSuccess($response)) // ex: log success, etc.
    ->then(fn(BatchCheckResponseInterface $response) => $response?->getResult()) // ex: return result
    ->recover(fn(Throwable $error) => []) // ex: ignore errors; fallback to empty array
    ->unwrap();

// $results = [
//   'alice-check' => true,
//   'bob-check' => false,
// ]
```

</details>

### List accessible objects

Use the `objects` helper to retrieve a list of objects a user can access.

```php
use function OpenFGA\objects;

// *What* documents can Alice edit?
$documents = objects(
    client: $client,
    store: $storeId,
    model: $modelId,
    user: 'user:alice',
    relation: 'editor',
    type: 'document'
);

// $documents = ['roadmap', 'budget', 'proposal']
```

> The `objects` helper wraps the Client `streamedListObjects` method and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>streamedListObjects</code> or <code>listObjects</code> methods directly if you need more control over the operation…</summary>

```php
use OpenFGA\Responses\StreamedListObjectsResponseInterface;
use Throwable;

// *What* documents can Alice edit?
$documents = $client->streamedListObjects(
    store: $storeId,
    model: $modelId,
    type: 'document',
    relation: 'editor',
    user: 'user:alice',
)
    ->failure(fn(Throwable $error) => logError($error)) // ex: log error, send alert, etc.
    ->success(fn(StreamedListObjectsResponseInterface $response) => logSuccess($response)) // ex: log success, etc.
    ->then(fn(StreamedListObjectsResponseInterface $response) => $response?->getObject()) // ex: return result
    ->recover(fn(Throwable $error) => []) // ex: ignore errors; fallback to empty array
    ->unwrap();

// $documents = ['roadmap', 'budget', 'proposal']
```

</details>

### Find users with permission

Use the `users` helper to retrieve a list of users who have a specific permission on an object.

```php
use function OpenFGA\users;

// *Who* can edit the "roadmap" document?
$editors = users(
    client: $client,
    store: $storeId,
    model: $modelId,
    object: 'document:roadmap',
    relation: 'editor'
);

foreach ($editors as $editor) {
    if ($editor->isUser()) {
        echo "User: " . $editor->getUserId() . "\n";
    } elseif ($editor->isUserset()) {
        echo "Group: " . $editor->getUsersetObject() . "\n";
    }
}
```

> The `users` helper wraps the Client `listUsers` method and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>listUsers</code> method directly if you need more control over the operation…</summary>

```php
use OpenFGA\Responses\ListUsersResponseInterface;
use Throwable;

// *Who* can edit the "roadmap" document?
$editors = $client->listUsers(
    store: $storeId,
    model: $modelId,
    object: 'document:roadmap',
    relation: 'editor'
)
    ->failure(fn(Throwable $error) => logError($error)) // ex: log error, send alert, etc.
    ->success(fn(ListUsersResponseInterface $response) => logSuccess($response)) // ex: log success, etc.
    ->then(fn(ListUsersResponseInterface $response) => $response->getUsers())
    ->recover(fn(Throwable $error) => []) // ex: ignore errors; fallback to empty array
    ->unwrap();

foreach ($editors as $editor) {
    if ($editor->isUser()) {
        echo "User: " . $editor->getUserId() . "\n";
    } elseif ($editor->isUserset()) {
        echo "Group: " . $editor->getUsersetObject() . "\n";
    }
}
```

</details>

### Expand relationships

When permissions aren't working as expected, use the `expand` helper to discovery why. It returns the complete relationship tree, and is useful for debugging complex permission structures or understanding why a user has (or doesn't have) access.

```php
use function OpenFGA\expand;

// *How* can a user be considered a viewer of the "roadmap" document?
$tree = expand(
    client: $client,
    store: $storeId,
    model: $modelId,
    relation: 'viewer',
    object: 'document:roadmap',
);

print_r($tree);
```

The `expand` helper wraps the Client `expand` method and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>expand</code> method directly if you need more control over the operation…</summary>

```php
use OpenFGA\Responses\ExpandResponseInterface;
use Throwable;
use function OpenFGA\tuple;

// *How* can a user be considered a viewer of the "roadmap" document?
$tree = $client->expand(
    store: $storeId,
    model: $modelId,
    tupleKey: tuple(
        user: '',  // Note: empty user for expand query
        relation: 'viewer',
        object: 'document:roadmap'
    )
)
    ->failure(fn(Throwable $error) => logError($error)) // ex: log error, send alert, etc.
    ->success(fn(ExpandResponseInterface $response) => logSuccess($response)) // ex: log success, etc.
    ->then(fn(ExpandResponseInterface $response) => $response?->getTree()) // ex: return result
    ->recover(fn(Throwable $error) => null) // ex: ignore errors; fallback to null
    ->unwrap();

print_r($tree);
```

</details>

## Advanced patterns

### Contextual tuples

Test "what-if" scenarios without permanently saving relationships. Perfect for previewing permission changes.

```php
use OpenFGA\{tuple, tuples, allowed};

// What if alice joins the engineering team?
$contextualTuple = tuple(
    user: 'user:alice',
    relation: 'member',
    object: 'team:engineering'
);

$wouldHaveAccess = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:technical-specs'
    ),
    contextualTuples: tuples($contextualTuple),
);

echo match ($wouldHaveAccess) {
    true => 'Alice WOULD have access',
    false => 'Alice WOULD NOT have access',
};
```

### Consistency levels

For read-after-write scenarios, you might need stronger consistency:

```php
use OpenFGA\Enums\Consistency;
use OpenFGA\{tuple, allowed};

$result = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:roadmap'
    ),
    consistency: Consistency::HIGHER_CONSISTENCY
);
```

#### Advanced error handling

Use enum-based exceptions for more precise error handling with i18n support:

```php
use OpenFGA\ClientInterface;
use OpenFGA\Exceptions\{ClientException, ClientError}
use function OpenFGA\{tuple};

// Define a robust permission checking service
// Note: This is an example helper class and not part of the SDK.
// It also assumes the existence of a CacheInterface for caching.
class PermissionService
{
    public function __construct(
        private ClientInterface $client,
        private string $storeId,
        private string $modelId,
        private CacheInterface $cache
    ) {}

    public function canAccess(string $userId, string $action, string $resourceId): bool
    {
        // Use result helper for cleaner handling
        return result(function() use ($userId, $action, $resourceId) {
            return $this->client->check(
                store: $this->storeId,
                model: $this->modelId,
                tupleKey: tuple("user:{$userId}", $action, $resourceId)
            );
        })
        ->then(fn($response) => $response->getAllowed())
        ->failure(function(Throwable $error) use ($userId, $action, $resourceId) {
            // Handle different error types with match expression
            if ($error instanceof ClientException) {
                // Type-safe handling of specific error cases
                return match($error->getError()) {
                    // For network timeouts, use cached result with short TTL
                    ClientError::Network => $this->getCachedPermission(
                        $userId,
                        $action,
                        $resourceId,
                        $defaultValue = false // Fail closed by default
                    ),

                    // For validation errors, log detailed context
                    ClientError::Validation => $this->handleValidationError(
                        $error->getContext(),
                        $userId,
                        $action,
                        $resourceId
                    ),

                    // For other cases, gracefully fail closed
                    default => false
                };
            }

            // For unexpected errors, log and fail closed
            logger()->error('Unexpected error during permission check', [
                'user' => $userId,
                'action' => $action,
                'resource' => $resourceId,
                'error' => $error->getMessage()
            ]);

            return false; // Secure default
        })
        ->unwrap();
    }

    private function getCachedPermission(string $userId, string $action, string $resourceId, bool $default): bool
    {
        $cacheKey = "permission:{$userId}:{$action}:{$resourceId}";
        return $this->cache->get($cacheKey, $default);
    }

    private function handleValidationError(array $context, string $userId, string $action, string $resourceId): bool
    {
        logger()->warning('Validation error during permission check', [
            'context' => $context,
            'user' => $userId,
            'action' => $action,
            'resource' => $resourceId
        ]);

        // Analyze context to determine appropriate fallback behavior
        // For this example, we'll fail closed
        return false;
    }
}
```

## Common Query Patterns

### Permission gates for routes

```php
use OpenFGA\ClientInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use function OpenFGA\{allowed, tuple};

// Middleware for route protection
class FgaAuthMiddleware
{
    public function __construct(
        private readonly ClientInterface $client,
        private readonly string $store,
        private readonly string $model
    ) {
    }

    public function handle(Request $request, Closure $next, string $relation): Response
    {
        $user = $request->user();
        $resource = $request->route('document'); // or extract from URL

        if (! allowed(
            client: $this->client,
            store: $this->store,
            model: $this->model,
            tuple: tuple("user:{$user->id}", $relation, "document:{$resource}"),
        )) {
            abort(403, "You don't have {$relation} access to this resource");
        }

        return $next($request);
    }
}
```

### Efficient data filtering

```php
use OpenFGA\ClientInterface;
use function OpenFGA\objects;

// Instead of checking each item individually
function getEditableDocuments(ClientInterface $client, string $storeId, string $modelId, string $userId): Collection
{
    // ❌ Don't do this - N+1 problem
    // return Document::all()->filter(fn($doc) =>
    //     allowed($client, $store, $model, tuple($userId, 'editor', $doc->id))
    // );

    // ✅ Do this - single API call
    $editableDocuments = objects(
        client: $client,
        store: $storeId,
        model: $modelId,
        user: "user:{$userId}",
        relation: 'editor',
        type: 'document'
    );

    return Document::whereIn('id', $editableDocuments)->get();
}
```

### Debugging permission issues

```php
use OpenFGA\ClientInterface;
use function OpenFGA\{allowed, expand, tuple, read};

// When "why doesn't this user have access?" questions arise
function debugUserAccess(ClientInterface $client, string $storeId, string $modelId, string $userId, string $documentId): void
{
    // Check direct permission
    $canEdit = allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple($userId, 'editor', $documentId)
    );

    echo 'Can edit: ' . ($canEdit === true ? 'Yes' : 'No') . "\n";

    // Show the permission tree
    $tree = expand(
        client: $client,
        store: $storeId,
        model: $modelId,
        relation: 'editor',
        object: $documentId
    );

    echo "Permission structure:\n";
    print_r($tree);

    // List all relationships for this document
    $allTuples = read(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple($documentId)
    );

    echo "All permissions:\n";

    foreach ($allTuples as $tuple) {
        echo "- {$tuple->getUser()} {$tuple->getRelation()}\n";
    }
}
```
