# Relationship Tuples

Relationship tuples are where the rubber meets the road. They're the actual permissions in your system - who can do what to which resource.

A tuple is simply: `(user, relation, object)`

For example: `(user:anne, editor, document:roadmap)` means "Anne can edit the roadmap document."

```php
<?php

use OpenFGA\Client;
use function OpenFGA\{tuple, tuples, write, delete};

// Basic setup - see Getting Started for full client initialization
$client = new Client(url: 'http://localhost:8080');
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
// Grant access to multiple users and revoke old permissions
$client->writeTuples(
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
)->unwrap();
```
## Reading Existing Permissions

Check what permissions exist by reading tuples:

```php
use OpenFGA\Models\TupleKey;

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
use OpenFGA\Models\{ConditionParameter, ConditionParameters, RelationshipCondition, TupleKey, TupleKeys};

// Only allow access during business hours
$client->writeTuples(
    store: $storeId,
    model: $modelId,
    writes: new TupleKeys([
        new TupleKey(
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
    ])
)->unwrap();
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

For checking permissions and querying relationships, see [Queries](Queries.md).
