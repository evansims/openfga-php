Relationship tuples are where the rubber meets the road. They're the actual permissions in your system - who can do what to which resource.

A tuple is simply: `(user, relation, object)`

For example: `(user:anne, editor, document:roadmap)` means "Anne can edit the roadmap document."

## Prerequisites

The examples in this guide assume you have the following setup:

```php
<?php

use OpenFGA\Client;
use OpenFGA\Exceptions\{ClientError, ClientException};
use OpenFGA\Models\{ConditionParameter, ConditionParameters, RelationshipCondition, TupleKey, TupleKeys};
use function OpenFGA\{tuple, tuples, write, delete, allowed, store, model, dsl, result, success, failure, unwrap};

// Client initialization - see Getting Started for full details
$client = new Client(url: 'http://localhost:8080');

// Store and model identifiers from your configuration
$storeId = 'your-store-id';
$modelId = 'your-model-id';
```

## Granting Permissions

Give someone access by writing a tuple:

```php
// Give Anne editor access to a document
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'editor', 'document:roadmap')
);
```

## Removing Permissions

Take away access by deleting a tuple:

```php
// Remove Anne's editor access
delete(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'editor', 'document:roadmap')
);
```

## Bulk Operations

Handle multiple permission changes in one transaction:

```php
// Grant access to multiple users
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuples(
        tuple('user:bob', 'viewer', 'document:roadmap'),
        tuple('user:charlie', 'editor', 'document:roadmap'),
        tuple('team:marketing#member', 'viewer', 'folder:campaigns')
    )
);

// Revoke old permissions
delete(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'owner', 'document:old-spec')
);
```

## Reading Existing Permissions

Check what permissions exist by reading tuples:

```php
// Find all permissions for a specific document
$response = $client->readTuples(
    store: $storeId,
    model: $modelId,
    tupleKey: new TupleKey(object: 'document:roadmap')
)->unwrap();

foreach ($response->getTuples() as $tuple) {
    echo "{$tuple->getUser()} has {$tuple->getRelation()} on {$tuple->getObject()}\n";
}
```

```php
// Find all documents Anne can edit
$response = $client->readTuples(
    store: $storeId,
    model: $modelId,
    tupleKey: new TupleKey(user: 'user:anne', relation: 'editor')
)->unwrap();

foreach ($response->getTuples() as $tuple) {
    echo "Anne can edit: {$tuple->getObject()}\n";
}
```

```php
// Paginate through all tuples
$continuationToken = null;

do {
    $response = $client->readTuples(
        store: $storeId,
        model: $modelId,
        pageSize: 100,
        continuationToken: $continuationToken
    )->unwrap();

    foreach ($response->getTuples() as $tuple) {
        // Process each tuple...
    }

    $continuationToken = $response->getContinuationToken();
} while ($continuationToken !== null);
```

## Advanced Patterns

### Conditional Tuples

Add conditions to make permissions context-dependent:

```php
// Only allow access during business hours
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple(
        user: 'user:contractor',
        relation: 'viewer',
        object: 'document:sensitive',
        condition: new RelationshipCondition(
            name: 'business_hours',
            context: [
                'timezone' => 'America/New_York'
            ]
        )
    )
);
```

### Tracking Changes

Monitor permission changes over time for auditing:

```php
// Get all permission changes for documents in the last hour
$startTime = (new DateTimeImmutable())->sub(new DateInterval('PT1H'));

$response = $client->listTupleChanges(
    store: $storeId,
    model: $modelId,
    type: 'document',
    startTime: $startTime
)->unwrap();

foreach ($response->getChanges() as $change) {
    $tuple = $change->getTupleKey();
    echo "{$change->getOperation()->value}: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
}
```

### Working with Groups

Grant permissions to groups instead of individual users:

```php
// Add user to a group
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:anne', 'member', 'team:engineering')
);

// Grant permission to the entire group
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('team:engineering#member', 'editor', 'document:technical-specs')
);
```

Now Anne can edit the technical specs because she's a member of the engineering team.

For checking permissions and querying relationships, see Queries.

## Error Handling with Tuples

When working with tuples, it's important to handle errors properly using the SDK's enum-based exception handling:

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
// Create a client with Spanish error messages
$client = new Client(
    url: 'https://api.openfga.example',
    language: 'es' // Spanish
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

After writing tuples to grant permissions, you'll want to verify those permissions are working correctly. The **Queries** guide covers how to check permissions, list user access, and discover relationships using the tuples you've created.