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
use function OpenFGA\Models\tuple;

// Can user:alice view document:roadmap?
$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer',
        object: 'document:roadmap'
    )
);

if ($result->unwrap()->getIsAllowed()) {
    // Alice can view the document
    echo "Access granted";
} else {
    // Alice cannot view the document
    echo "Access denied";
}
```

### Real-world usage

```php
function canUserEdit(string $userId, string $documentId): bool 
{
    $result = $client->check(
        tupleKey: tuple(
            user: "user:{$userId}",
            relation: 'editor',
            object: "document:{$documentId}"
        )
    );
    
    return $result->unwrap()->getIsAllowed();
}

// In your controller
if (!canUserEdit($currentUserId, $documentId)) {
    throw new ForbiddenException('You cannot edit this document');
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
use function OpenFGA\Models\{tuple, tuples};

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

All query methods return Result objects. Handle errors gracefully:

```php
$result = $client->check(
    tupleKey: tuple(
        user: 'user:alice',
        relation: 'viewer', 
        object: 'document:roadmap'
    )
);

$result
    ->success(fn($response) => echo "Check succeeded")
    ->failure(fn($error) => logger()->error("Permission check failed", ['error' => $error]))
    ->unwrap(); // Throws on failure
```

## What's next?

Now that you can query permissions, you might want to:

- [Write assertions](Assertions.md) to test your permission logic
- Learn about [error handling](Results.md) for production apps
- Explore [authorization models](Models.md) for complex scenarios
