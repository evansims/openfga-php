# Queries

Ready to check permissions? Once you've set up your [authorization model](Models.md) and [relationship tuples](Tuples.md), it's time to actually use them. This is where OpenFGA shines - answering permission questions in real-time.

## What are queries?

Queries let you ask OpenFGA about permissions. There are four types:

- **Check permissions** - "Can Alice edit this document?"
- **List accessible objects** - "What documents can Alice edit?"
- **Find users with permission** - "Who can edit this document?"
- **Expand relationships** - "How does Alice have edit access?" (for debugging)

## Check permissions

This is the most common query. Use it to enforce access control in your app.

```php
use OpenFGA\Models\TupleKey;
use function OpenFGA\tuple;

// Can user:alice view document:roadmap?
$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:roadmap'
    )
);

if ($result->unwrap()->getAllowed()) {
    // Alice can view the document
    echo "Access granted";
} else {
    // Alice cannot view the document
    echo "Access denied";
}
```

### Real-world usage

```php
use function OpenFGA\{allowed, tuple};

function canUserEdit(ClientInterface $client, string $storeId, string $modelId, string $userId, string $documentId): bool
{
    return allowed(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuple: tuple("user:{$userId}", 'editor', "document:{$documentId}")
    );
}

// In your controller or middleware
if (!canUserEdit($client, $storeId, $modelId, $currentUserId, $documentId)) {
    throw new ForbiddenException('You cannot edit this document');
}

// For multiple checks, use the Result pattern for better error handling
$editResult = $client->check(
    tupleKey: tuple("user:{$userId}", 'editor', "document:{$documentId}")
);

if ($editResult->failed()) {
    logger()->warning('Permission check failed', ['error' => $editResult->err()->getMessage()]);
    // Handle error - maybe return false or show a generic error
}
```

## List accessible objects

Perfect for building dashboards and filtered lists. Shows what a user can access.

```php
// What documents can alice edit?
$result = $client->listObjects(
    user: 'user:alice',
    relation: 'editor',
    type: 'document'
);

$documentIds = $result->unwrap()->getObjects();
// Returns: ['roadmap', 'budget', 'proposal']
```

### Building a document list

```php
function getEditableDocuments(string $userId): array
{
    $result = $client->listObjects(
        user: "user:{$userId}",
        relation: 'editor',
        type: 'document'
    );

    $documentIds = $result->unwrap()->getObjects();

    // Fetch full document details from your database
    return Document::whereIn('id', $documentIds)->get();
}
```

## Find users with permission

Great for admin interfaces and sharing features. Shows who has access to something.

```php
// Who can edit document:roadmap?
$result = $client->listUsers(
    object: 'document:roadmap',
    relation: 'editor'
);

$users = $result->unwrap()->getUsers();

foreach ($users as $user) {
    if ($user->isUser()) {
        echo "User: " . $user->getUserId() . "\n";
    } elseif ($user->isUserset()) {
        echo "Group: " . $user->getUsersetObject() . "\n";
    }
}
```

### Building a sharing interface

```php
function getDocumentEditors(string $documentId): array
{
    $result = $client->listUsers(
        object: "document:{$documentId}",
        relation: 'editor'
    );

    $editors = [];
    foreach ($result->unwrap()->getUsers() as $user) {
        if ($user->isUser()) {
            $editors[] = [
                'type' => 'user',
                'id' => $user->getUserId(),
                'name' => User::find($user->getUserId())->name
            ];
        }
    }

    return $editors;
}
```

## Expand relationships (debugging)

When permissions aren't working as expected, use expand to see why. It shows the complete relationship tree.

```php
use OpenFGA\Models\TupleKey;

// How can anyone be a viewer of document:roadmap?
$result = $client->expand(
    tupleKey: new TupleKey(
        relation: 'viewer',
        object: 'document:roadmap'
        // Note: no user specified for expand
    )
);

$tree = $result->unwrap()->getTree();
print_r($tree->toArray()); // Shows the complete permission tree
```

This is mainly useful for debugging complex permission structures or understanding why a user has (or doesn't have) access.

## Advanced patterns

### Contextual tuples

Test "what-if" scenarios without permanently saving relationships. Perfect for previewing permission changes.

```php
use function OpenFGA\{tuple, tuples};

// What if alice joins the engineering team?
$contextualTuple = tuple(
    user: 'user:alice',
    relation: 'member',
    object: 'team:engineering'
);

$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:technical-specs'
    ),
    contextualTuples: tuples($contextualTuple)
);

// This check includes the temporary relationship
$wouldHaveAccess = $result->unwrap()->getIsAllowed();
```

### Consistency levels

For read-after-write scenarios, you might need stronger consistency:

```php
use OpenFGA\Models\Enums\Consistency;

$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:roadmap'
    ),
    consistency: Consistency::HIGHER_CONSISTENCY
);
```

### Error handling

All query methods return Result objects. Handle errors gracefully using the Result pattern and helper functions:

```php
use function OpenFGA\{tuple, result, success, failure, unwrap};
use OpenFGA\Exceptions\{ClientError, ClientException, NetworkError, NetworkException};

// Basic error handling
$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:roadmap'
    )
);

$result
    ->success(fn($response) => echo "Check succeeded: " . ($response->getAllowed() ? 'Allowed' : 'Denied'))
    ->failure(fn($error) => logger()->error("Permission check failed", [
        'error_type' => $error::class,
        'error_detail' => $error instanceof ClientException ? $error->getError()->name : 'unknown'
    ]))
    ->unwrap(); // Throws on failure
```

#### Advanced error handling with enum-based exceptions

Use enum-based exceptions for more precise error handling with i18n support:

```php
// Define a robust permission checking service
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
use Closure;
use Illuminate\Http\Request;
use OpenFGA\ClientInterface;
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

        if (!allowed($this->client, $this->store, $this->model,
                     tuple("user:{$user->id}", $relation, "document:{$resource}"))) {
            abort(403, "You don't have {$relation} access to this resource");
        }

        return $next($request);
    }
}
```

### Efficient data filtering

```php
// Instead of checking each item individually
function getEditableDocuments(string $userId): Collection
{
    // ❌ Don't do this - N+1 problem
    // return Document::all()->filter(fn($doc) =>
    //     allowed($client, $store, $model, tuple($userId, 'editor', $doc->id))
    // );

    // ✅ Do this - single API call
    $editableIds = $client->listObjects(
        user: "user:{$userId}",
        relation: 'editor',
        type: 'document'
    )->unwrap()->getObjects();

    return Document::whereIn('id', $editableIds)->get();
}
```

### Debugging permission issues

```php
// When "why doesn't this user have access?" questions arise
function debugUserAccess(string $userId, string $documentId): void
{
    // Check direct permission
    $canEdit = $client->check(
        tupleKey: tuple($userId, 'editor', $documentId)
    )->unwrap();

    echo "Can edit: " . ($canEdit->getAllowed() ? 'Yes' : 'No') . "\n";

    // Show the permission tree
    $tree = $client->expand(
        tupleKey: new TupleKey(relation: 'editor', object: $documentId)
    )->unwrap();

    echo "Permission structure:\n";
    print_r($tree->toArray());

    // List all relationships for this document
    $allTuples = $client->readTuples(
        tupleKey: new TupleKey(object: $documentId)
    )->unwrap();

    echo "All permissions:\n";
    foreach ($allTuples->getTuples() as $tuple) {
        echo "- {$tuple->getUser()} {$tuple->getRelation()}\n";
    }
}
```

## What's next?

Now that you can query permissions effectively:

**Essential next steps:**

- **[Results →](Results.md)** - Handle errors gracefully in production
- **[Tuples →](Tuples.md)** - Learn to grant and revoke permissions
- **[Models →](Models.md)** - Build more sophisticated permission rules

**Advanced topics:**

- **[Assertions →](Assertions.md)** - Test your permission logic automatically
- **[Concurrency →](Concurrency.md)** - Optimize batch operations
- **[Observability →](Observability.md)** - Monitor query performance
