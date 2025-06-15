**Relationship tuples are where the rubber meets the road.** They're the actual permissions in your system - they define who can do what to which resource.

## Prerequisites

The examples in this guide assume you have the following setup:

```php
use OpenFGA\Client;

// Initialize the client
$client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');

// Store and model identifiers from your configuration
$storeId = 'your-store-id';
$modelId = 'your-model-id';
```

## Granting Permissions

Use the `write` helper to give someone access:

```php
use function OpenFGA\{write, tuple};

// Give Anne editor access to the "roadmap" document
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'editor', 'document:roadmap')
);
```

## Removing Permissions

Use the `delete` helper to take away access:

```php
use function OpenFGA\{delete, tuple};

// Remove Anne's editor access to the "roadmap" document
delete(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'editor', 'document:roadmap')
);
```

## Bulk Operations

Use the `writes` helper to handle multiple permission changes in one transaction:

```php
use function OpenFGA\{writes, tuple, tuples};

writes(
    client: $client,
    store: $storeId,
    model: $modelId,
    writes: tuples(
        tuple('user:bob', 'viewer', 'document:roadmap'),
        tuple('user:charlie', 'editor', 'document:roadmap'),
        tuple('team:marketing#member', 'viewer', 'folder:campaigns')
    ),
    deletes: tuples(
        tuple('user:anne', 'owner', 'document:old-spec')
    )
);
```

## Reading Existing Permissions

Use the `read` helper to check what permissions exist:

```php
use function OpenFGA\{read, tuple};

// Find all permissions for a specific document
$tuples = read(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(object: 'document:roadmap')
);

foreach ($tuples as $tuple) {
    echo "{$tuple->getUser()} has {$tuple->getRelation()} on {$tuple->getObject()}\n";
}
```

```php
use function OpenFGA\{read, tuple};

// Find all documents Anne can edit
$tuples = read(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(user: 'user:anne', relation: 'editor')
);

echo "Anne can edit:\n";

foreach ($tuples as $tuple) {
    echo "{$tuple->getObject()}\n";
}
```

## Advanced Patterns

### Conditional Tuples

Use conditions to make permissions context-dependent:

```php
use OpenFga\Models\RelationshipCondition;
use function OpenFGA\{write, tuple};

$businessHoursCondition = new RelationshipCondition(
    name: 'business_hours',
    context: [
        'timezone' => 'America/Chicago'
    ]
);

// Only allow access during business hours
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:contractor',
        relation: 'viewer',
        object: 'document:sensitive',
        condition: $businessHoursCondition
    )
);
```

### Auditing Changes

Monitor permission changes over time for auditing:

```php
use DateTimeImmutable;
use DateInterval;
use function OpenFGA\changes;

// Get all changes for documents in the last hour
$startTime = (new DateTimeImmutable())->sub(new DateInterval('PT1H'));

$changes = changes(
    client: $client,
    store: $storeId,
    model: $modelId,
    type: 'document',
    startTime: $startTime
);

foreach ($changes as $change) {
    $tuple = $change->getTupleKey();
    echo "{$change->getOperation()->value}: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
}
```

### Working with Groups

Use the `write` helper to grant permissions to groups instead of individual users:

```php
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        // Add user to a group
        tuple('user:anne', 'member', 'team:engineering'),
        // Grant permission to the entire group
        tuple('team:engineering#member', 'editor', 'document:technical-specs')
    )
);
```

Now Anne can edit the technical specs because she's a member of the engineering team.

For checking permissions and querying relationships, see [Queries](Queries.md).

## Error Handling with Tuples

The SDK has a powerful enum-based exception handling system that allows you to handle errors in a type-safe way.

```php
// Example: Writing tuples with robust error handling
function addUserToDocument(string $userId, string $documentId, string $role = 'viewer'): bool
{
    global $client, $storeId, $modelId;

    // Use result helper for cleaner error handling
    return result(function() use ($client, $storeId, $modelId, $userId, $documentId, $role) {
        return write(
            client: $client,
            store: $storeId,
            model: $modelId,
            tuples: tuple("user:{$userId}", $role, "document:{$documentId}")
        );
    })
    ->success(function() {
        logger()->info('Access granted', [
            'user' => $userId,
            'document' => $documentId,
            'role' => $role
        ]);
        return true;
    })
    ->failure(function(Throwable $error) use ($userId, $documentId, $role) {
        // Enum-based error handling with match expression
        if ($error instanceof ClientException) {
            match($error->getError()) {
                // Handle validation errors specifically
                ClientError::Validation => logger()->warning(
                    'Validation error granting access',
                    ['context' => $error->getContext()]
                ),

                // Handle authorization model mismatches
                ClientError::InvalidConfiguration => logger()->error(
                    'Model configuration error',
                    ['message' => $error->getMessage()]
                ),

                // Default case for other client errors
                default => logger()->error(
                    'Failed to grant access',
                    ['error_type' => $error->getError()->name]
                )
            };
        } else {
            // Handle unexpected errors
            logger()->error('Unexpected error granting access', [
                'error' => $error->getMessage(),
                'user' => $userId,
                'document' => $documentId
            ]);
        }

        return false;
    })
    ->unwrap();
}
```

### Supporting Multiple Languages

The error messages from tuple operations will automatically use the language configured in your client:

```php
use OpenFGA\{Client, Language};
use function OpenFGA\{write, tuple};

// Create a client with Spanish error messages
$client = new Client(
    url: 'https://api.openfga.example',
    language: Language::Spanish
);

try {
    // Attempt to write an invalid tuple
    write(
        client: $client,
        store: $storeId,
        model: $modelId,
        tuples: tuple('', 'viewer', 'document:report')
    );
} catch (ClientException $e) {
    // The error message will be in Spanish
    echo $e->getMessage(); // "El identificador del usuario no puede estar vacío"

    // But the error enum remains the same for consistent handling
    if ($e->getError() === ClientError::Validation) {
        // Handle validation error regardless of language
    }
}
```

## What's Next?

After writing tuples to grant permissions, you'll want to verify those permissions are working correctly. The [Queries](Queries.md) guide covers how to check permissions, list user access, and discover relationships using the tuples you've created.
