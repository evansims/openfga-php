# Performing Relationship Queries in OpenFGA

Once you have an [Authorization Model](AuthorizationModels.md) in place and have populated it with [Relationship Tuples](RelationshipTuples.md), the next step is to ask OpenFGA about effective permissions. This is done through **Relationship Queries**. These queries allow you to determine what actions users can take on which resources based on the defined model and stored tuples.

This guide will walk you through the main types of queries:

- **`check()`**: Answers: "Does user X have permission Y on object Z?"
- **`expand()`**: Answers: "How does user X have permission Y on object Z?" (by showing the relationship tree).
- **`listUsers()`**: Answers: "Which users have permission Y on object Z?"
- **`listObjects()`**: Answers: "What objects of type T can user X access with permission Y?"

## Prerequisites

These examples assume:

1. You have initialized the SDK client as `$client`.
2. You have a `storeId` and have set it on the client: `$client->setStore($storeId);`.
3. You have a `modelId` (Authorization Model ID) and have set it on the client: `$client->setModel($modelId);`.
4. Refer to [Getting Started](GettingStarted.md), [Stores](Stores.md), and [Understanding Authorization Models](AuthorizationModels.md) for these initial setup steps.

For robust error handling beyond the `unwrap()` helper shown in these examples, please see our guide on [Results and Error Handling](Results.md). All client methods return `Result` objects.

```php
<?php
// Common setup for examples:
require_once __DIR__ . '/vendor/autoload.php'; // If running examples standalone

use OpenFGA\Client;
use OpenFGA\Results\unwrap;
use OpenFGA\Models\TupleKey;
use OpenFGA\Models\TupleKeys; // For contextual tuples
use OpenFGA\Enum\Consistency;  // For consistency options if needed
// Response interfaces for type hinting (optional but good practice)
use OpenFGA\Responses\CheckResponseInterface;
use OpenFGA\Responses\ExpandResponseInterface;
use OpenFGA\Responses\ListUsersResponseInterface;
use OpenFGA\Responses\ListObjectsResponseInterface;
use OpenFGA\Models\UsersetTree; // For Expand response

// Assuming $client is initialized and storeId & modelId are set:
// $fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
// $storeId = $_ENV['FGA_STORE_ID'] ?? 'your_test_store_id';
// $modelId = $_ENV['FGA_MODEL_ID'] ?? 'your_test_model_id';
// $client = new Client(url: $fgaApiUrl);
// $client->setStore($storeId);
// $client->setModel($modelId); // Important: sets the model for these query operations
?>
```

## 1. Check (`Client::check()`)

**Question:** "Does this user have this specific permission on this object?"

The `check()` method is the most fundamental query. It returns a simple boolean answer: `true` if the permission is granted, `false` otherwise.

**Use Cases:**

- Enforcing access control in your application (e.g., "Can the current user view this page?").
- Verifying if a specific user has a particular role or capability on a resource.

**Parameters:**

- `tupleKey` (required `OpenFGA\Models\TupleKey`): A `TupleKey` object specifying:
  - `user` (string): The user or userset (e.g., `user:anne`, `group:editors#member`). **Required.**
  - `relation` (string): The permission/relation to check (e.g., `viewer`, `can_edit`). **Required.**
  - `object` (string): The specific object (e.g., `document:roadmap`, `folder:secrets`). **Required.**
- `contextual_tuples` (optional `OpenFGA\Models\TupleKeys`): A collection of `TupleKey` objects to consider only for this specific check, without permanently writing them. See [Contextual Tuples](#contextual-tuples-what-if-scenarios) below.
- `consistency` (optional `OpenFGA\Enum\Consistency`): Specifies read consistency. Defaults to `Consistency::NONE`. See [Relationship Tuples `consistency` explanation](RelationshipTuples.md#reading-tuples-clientreadtuples) for details.
- `authorization_model_id` (optional string): Overrides the model ID set on the client for this specific call.

**Example:**

```php
<?php
// Check if user:anne is a viewer of document:roadmap
$checkTupleKey = new TupleKey(
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:roadmap'
);

try {
    /** @var CheckResponseInterface $response */
    $response = unwrap($client->check(tupleKey: $checkTupleKey));

    if ($response->getIsAllowed()) {
        echo "User 'user:anne' IS ALLOWED to 'viewer' 'document:roadmap'.\n";
    } else {
        echo "User 'user:anne' IS NOT ALLOWED to 'viewer' 'document:roadmap'.\n";
        // The response might contain a resolution string in this case,
        // which can give hints if expand results are too complex.
        // echo "Resolution: " . $response->getResolution() . "\n";
    }
} catch (Throwable $e) {
    echo "Error during check: " . $e->getMessage() . "\n";
}
?>
```

**Response Interpretation:**
The primary method on the `CheckResponseInterface` is `getIsAllowed()`, which returns a boolean.

## 2. Expand (`Client::expand()`)

**Question:** "How does this user (or any user with this relation) have this permission on this object?"

The `expand()` method returns a tree structure (`UsersetTree`) showing all the ways a specific `relation` on an `object` can be satisfied. It reveals the "why" behind a permission.

**Use Cases:**

- Debugging permissions: Understanding why a user has (or doesn't have) a certain access.
- Displaying detailed access information to administrators.
- Auditing how permissions are derived.

**Parameters:**

- `tupleKey` (required `OpenFGA\Models\TupleKey`): A `TupleKey` object specifying:
  - `relation` (string): The permission/relation to expand (e.g., `viewer`). **Required.**
  - `object` (string): The specific object (e.g., `document:roadmap`). **Required.**
  - `user` is NOT part of the `tupleKey` for `expand`. Expand shows all paths to the relation for the object.
- `consistency` (optional `OpenFGA\Enum\Consistency`): Specifies read consistency.
- `authorization_model_id` (optional string): Overrides the client's model ID.

**Example:**

```php
<?php
// Expand the 'viewer' relation for 'document:roadmap'
$expandTupleKey = new TupleKey(
    relation: 'viewer',
    object: 'document:roadmap'
    // 'user' is not specified here
);

try {
    /** @var ExpandResponseInterface $response */
    $response = unwrap($client->expand(tupleKey: $expandTupleKey));

    $tree = $response->getTree(); // Returns an OpenFGA\Models\UsersetTree object

    echo "Expansion tree for 'viewer' of 'document:roadmap':\n";
    // A simple way to visualize the tree (you might want a recursive function for complex trees)
    print_r($tree->toArray()); // Converts the tree to an array for inspection

    // Example of how you might manually inspect parts of the tree:
    // echo "Root node type: " . $tree->getRoot()?->getType() . "\n";
    // if ($tree->getRoot()?->getLeaf()) {
    //     echo "Leaf users: \n";
    //     print_r($tree->getRoot()?->getLeaf()->getUsers()->getUsers());
    // }

} catch (Throwable $e) {
    echo "Error during expand: " . $e->getMessage() . "\n";
}
?>
```

**Response Interpretation:**
The `ExpandResponseInterface::getTree()` method returns a `UsersetTree` object. This tree can be complex, representing direct assignments (`LeafNode`), unions (`UnionNode`), intersections (`IntersectionNode`), or computed relations (`DifferenceNode`). Each node shows how usersets (like `user:anne` or `group:editors#member`) contribute to the target relation.

## 3. List Users (`Client::listUsers()`)

**Question:** "Which users have this specific permission on this object?"

The `listUsers()` method returns a list of users (and usersets) that have a given `relation` to a specific `object`.

**Use Cases:**

- Displaying a list of users who can access a resource.
- Administrative tasks, like managing who has a particular role.

**Parameters:**

- `object_type` (string): The type of the object (e.g., `document`). **Required.**
- `object_id` (string): The ID of the object (e.g., `roadmap`). **Required.**
- `relation` (string): The permission/relation to list users for (e.g., `viewer`). **Required.**
- `user_filters` (optional array of `OpenFGA\Models\UserFilter`): Filters the results to specific user types and relations (e.g., only return users of type `user`, or only users who are `member` of `group:engineering`). Each `UserFilter` has a `type` (e.g., `user`) and an optional `relation` (e.g., `member`).
- `contextual_tuples` (optional `OpenFGA\Models\TupleKeys`): See [Contextual Tuples](#contextual-tuples-what-if-scenarios).
- `consistency` (optional `OpenFGA\Enum\Consistency`): Specifies read consistency.
- `authorization_model_id` (optional string): Overrides the client's model ID.

**Example:**

```php
<?php
// List all users who are 'viewers' of 'document:roadmap'
try {
    /** @var ListUsersResponseInterface $response */
    $response = unwrap($client->listUsers(
        objectType: 'document',
        objectId: 'roadmap',
        relation: 'viewer'
        // Example with user_filters:
        // userFilters: [new \OpenFGA\Models\UserFilter(type: 'user')] // Only list direct users
    ));

    $users = $response->getUsers(); // Array of OpenFGA\Models\User objects

    echo "Users who are 'viewers' of 'document:roadmap':\n";
    if (empty($users)) {
        echo "No users found.\n";
    } else {
        foreach ($users as $user) {
            if ($user->isUser()) { // Direct user, e.g., user:anne
                echo "- User ID: " . $user->getUserId() . "\n";
            } elseif ($user->isUserset()) { // Userset, e.g., group:engineering#member
                echo "- Userset: {$user->getUsersetObject()}#{$user->getUsersetRelation()}\n";
            } elseif ($user->isWildcard()) { // Wildcard, e.g. user:*
                 echo "- Wildcard User: {$user->getUserType()}:*\n";
            }
        }
    }
} catch (Throwable $e) {
    echo "Error during listUsers: " . $e->getMessage() . "\n";
}
?>
```

**Response Interpretation:**
The `ListUsersResponseInterface::getUsers()` method returns an array of `OpenFGA\Models\User` objects. Each `User` object can represent a direct user (`isUser()`, `getUserId()`), a userset (`isUserset()`, `getUsersetObject()`, `getUsersetRelation()`), or a wildcard (`isWildcard()`, `getUserType()`).

## 4. List Objects (`Client::listObjects()`)

**Question:** "What objects of a specific type can a given user access with a particular permission?"

The `listObjects()` method returns a list of object IDs of a specified `type` that a given `user` has a certain `relation` to.

**Use Cases:**

- Displaying all documents a user can view.
- Filtering a list of resources based on the current user's permissions.

**Parameters:**

- `user` (string): The user or userset (e.g., `user:anne`, `group:editors#member`). **Required.**
- `relation` (string): The permission/relation to check for (e.g., `viewer`). **Required.**
- `type` (string): The type of objects to list (e.g., `document`, `folder`). **Required.**
- `contextual_tuples` (optional `OpenFGA\Models\TupleKeys`): See [Contextual Tuples](#contextual-tuples-what-if-scenarios).
- `consistency` (optional `OpenFGA\Enum\Consistency`): Specifies read consistency.
- `authorization_model_id` (optional string): Overrides the client's model ID.

**Example:**

```php
<?php
// List all 'document' objects that 'user:anne' can 'viewer'
try {
    /** @var ListObjectsResponseInterface $response */
    $response = unwrap($client->listObjects(
        user: 'user:anne',
        relation: 'viewer',
        type: 'document'
    ));

    $objectIds = $response->getObjects(); // Array of object IDs (strings)

    echo "User 'user:anne' can 'viewer' the following 'document' objects:\n";
    if (empty($objectIds)) {
        echo "No documents found.\n";
    } else {
        foreach ($objectIds as $objectId) {
            echo "- document:" . $objectId . "\n";
        }
    }
} catch (Throwable $e) {
    echo "Error during listObjects: " . $e->getMessage() . "\n";
}
?>
```

**Response Interpretation:**
The `ListObjectsResponseInterface::getObjects()` method returns an array of strings, where each string is an object ID of the specified type.

## Contextual Tuples: "What-If" Scenarios

Contextual tuples are a powerful feature that allows you to include additional relationship tuples _only for the scope of a single query_ (`check`, `listUsers`, `listObjects`). These tuples are not permanently written to the store.

**Use Cases:**

- **"What-if" analysis:** Previewing the effect of granting a new permission before actually writing it. For example, "If I make user:bob an editor of document:budget, will they also become a viewer?"
- **Temporary context:** Incorporating temporary conditions or relationships that are only relevant for a specific request.

**Example with `check()`:**

Imagine `user:temp-contractor` does not normally have `viewer` access to `document:confidential`. We want to check if giving them a temporary `member` role in `group:project-alpha` (which _does_ have `viewer` access to the document) would grant them access.

```php
<?php
// Assume user:temp-contractor is NOT normally a viewer of document:confidential
// Assume group:project-alpha#member IS a viewer of document:confidential in the model/stored tuples

$checkTupleKey = new TupleKey(
    user: 'user:temp-contractor',
    relation: 'viewer',
    object: 'document:confidential'
);

// Contextual tuple: "user:temp-contractor is a member of group:project-alpha"
$contextualTuple = new TupleKey(
    user: 'user:temp-contractor',
    relation: 'member',
    object: 'group:project-alpha'
);
$contextualTuples = new TupleKeys([$contextualTuple]);

try {
    /** @var CheckResponseInterface $response */
    $response = unwrap($client->check(
        tupleKey: $checkTupleKey,
        contextualTuples: $contextualTuples
    ));

    if ($response->getIsAllowed()) {
        echo "YES, if 'user:temp-contractor' becomes 'member' of 'group:project-alpha', they CAN 'viewer' 'document:confidential'.\n";
    } else {
        echo "NO, even if 'user:temp-contractor' becomes 'member' of 'group:project-alpha', they STILL CANNOT 'viewer' 'document:confidential'.\n";
    }
} catch (Throwable $e) {
    echo "Error during check with contextual tuples: " . $e->getMessage() . "\n";
}
?>
```

In this example, the `contextualTuple` is only considered for this single `check()` call and is not saved in the database.

## Next Steps

With a solid understanding of how to query your OpenFGA system, you can now:

- **[Write Assertions (Assertions.md)](Assertions.md):** Create tests to validate that your authorization model, tuples, and queries behave as expected under various scenarios. This is crucial for maintaining a reliable authorization system.
- Review other documentation sections if you haven't already, to ensure a comprehensive understanding of the SDK and OpenFGA concepts.
