# Managing Relationship Tuples in OpenFGA

**Relationship Tuples** are the actual data that define permissions in OpenFGA. They are the facts that connect a `user` (or a group of users, known as a userset) to a specific `relation` on an `object`. Tuples bring your [Authorization Model](AuthorizationModels.md) to life by populating it with specific permission assignments.

The fundamental structure of a tuple is:

`(<user>, <relation>, <object>)`

Let's break down each component:

*   **`user`**: This identifies who or what is being granted the relationship. It can be:
    *   A specific user ID: e.g., `user:anne`, `employee:bob@company.com`. The `user:` part is a type, and `anne` is the ID.
    *   A userset (a group defined by a type and relation): e.g., `group:admins#member`. This means any user who has the `member` relation to the `group:admins` object.
    *   Another object type (for object-to-object relationships): e.g., `document:budget` (if, for example, a `folder` can grant permissions to documents it contains).
    *   The wildcard `*` (for public access): e.g., `user:*` means any user.

*   **`relation`**: This is the specific relationship being granted. It must be a relation defined in your active Authorization Model for the given object type.
    *   Examples: `viewer`, `editor`, `owner`, `can_share`, `member`.

*   **`object`**: This identifies the specific resource to which the relationship applies.
    *   Examples: `document:roadmap`, `folder:budgets`, `report:2023-annual`. The `document:` part is a type, and `roadmap` is the ID.

For example, the tuple `(user:anne, viewer, document:roadmap)` means "User Anne is a viewer of the document Roadmap."

This guide demonstrates how to manage these tuples using the OpenFGA PHP SDK.

## Prerequisites

These examples assume:

1.  You have initialized the SDK client as `$client`.
2.  You have a `storeId` and have set it on the client: `$client->setStore($storeId);`.
3.  You have a `modelId` (Authorization Model ID) and have set it on the client: `$client->setModel($modelId);`.
4.  Refer to [Getting Started](GettingStarted.md), [Stores](Stores.md), and [Understanding Authorization Models](AuthorizationModels.md) for these initial setup steps.

For robust error handling beyond the `unwrap()` helper shown in these examples, please see our guide on [Results and Error Handling](Results.md). All client methods return `Result` objects.

```php
<?php
// Common setup for examples:
require_once __DIR__ . '/vendor/autoload.php'; // If running examples standalone

use OpenFGA\Client;
use OpenFGA\Results\unwrap;
use OpenFGA\Models\TupleKey; // Represents a single tuple
use OpenFGA\Models\TupleKeys; // Represents a collection of tuples for writing/deleting
use OpenFGA\Enum\Consistency; // For ReadTuples consistency options
use OpenFGA\Responses\ReadTuplesResponseInterface;
use OpenFGA\Responses\WriteTuplesResponseInterface;
use OpenFGA\Responses\ListTupleChangesResponseInterface;

// Assuming $client is initialized and storeId & modelId are set:
// $fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
// $storeId = $_ENV['FGA_STORE_ID'] ?? 'your_test_store_id';
// $modelId = $_ENV['FGA_MODEL_ID'] ?? 'your_test_model_id';
// $client = new Client(url: $fgaApiUrl);
// $client->setStore($storeId);
// $client->setModel($modelId); // Important: sets the model for these operations
?>
```

## Structure of a `TupleKey` Object

In the PHP SDK, a single relationship tuple is represented by an `OpenFGA\Models\TupleKey` object. It has the following main properties:

*   `user`: (string) The user or userset (e.g., `user:anne`, `group:admins#member`).
*   `relation`: (string) The relation (e.g., `viewer`).
*   `object`: (string) The object (e.g., `document:roadmap`).
*   `condition` (optional `OpenFGA\Models\RelationshipCondition`): For advanced scenarios where the tuple is only valid if a named condition (defined in the Authorization Model) evaluates to true, given some context.
    *   `name`: (string) The name of the condition as defined in the model.
    *   `context`: (array|object, optional) A key-value map providing contextual information for the condition evaluation.

We will focus on non-conditional tuples in these examples.

## Writing Tuples (`Client::writeTuples()`)

The `writeTuples()` method allows you to add (write) and remove (delete) relationship tuples in your store. This is how you grant or revoke permissions.

**Key Features:**

*   **Transactional:** All writes and deletes within a single `writeTuples()` call are processed as a single transaction. Either all changes apply, or none do.
*   **Batch Operations:** You can write and delete multiple tuples in one call.

**Parameters:**

*   `writes` (optional `OpenFGA\Models\TupleKeys`): A collection of `TupleKey` objects to be created.
*   `deletes` (optional `OpenFGA\Models\TupleKeys`): A collection of `TupleKey` objects to be removed.
*   `authorization_model_id` (optional string): If you haven't set the model ID on the client using `$client->setModel()`, or if you need to override it for this specific call, you can provide it here.

```php
<?php
// Example: Writing and Deleting Tuples

// Tuples to be written
$tupleKeyAnneViewer = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap');
$tupleKeyBobEditor = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:roadmap');
$tupleKeyCharlieViewerReport = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'report:2023-summary');

$tupleKeysToWrite = new TupleKeys([$tupleKeyAnneViewer, $tupleKeyBobEditor, $tupleKeyCharlieViewerReport]);

// Tuple to be deleted (let's assume this was written in a previous operation)
$tupleKeyAnneOldAccess = new TupleKey(user: 'user:anne', relation: 'editor', object: 'document:archive');
$tupleKeysToDelete = new TupleKeys([$tupleKeyAnneOldAccess]);

try {
    /** @var WriteTuplesResponseInterface $response */
    // For this call, we rely on the model ID set via $client->setModel()
    $response = unwrap($client->writeTuples(
        writes: $tupleKeysToWrite,
        deletes: $tupleKeysToDelete
    ));

    echo "Tuples written/deleted successfully.\n";
    // $response will be empty on success for writeTuples as of OpenFGA 1.5.0+
    // Older versions might have returned an object with a `writes` array.

} catch (Throwable $e) {
    echo "Error writing/deleting tuples: " . $e->getMessage() . "\n";
    // This could be due to an invalid model ID, malformed tuples,
    // or issues with the OpenFGA server.
}

// Example: Writing a single tuple
$singleTuple = new TupleKey(user: 'user:diana', relation: 'owner', object: 'folder:projects');
try {
    unwrap($client->writeTuples(writes: new TupleKeys([$singleTuple])));
    echo "Single tuple (Diana owner of folder:projects) written successfully.\n";
} catch (Throwable $e) {
    echo "Error writing single tuple: " . $e->getMessage() . "\n";
}
?>
```

## Reading Tuples (`Client::readTuples()`)

The `readTuples()` method retrieves relationship tuples that match a specified filter. You can filter by any combination of user, relation, and object.

**Parameters:**

*   `tupleKey` (optional `OpenFGA\Models\TupleKey`): A `TupleKey` object used as a filter.
    *   If all parts (`user`, `relation`, `object`) are provided, it fetches that specific tuple (if it exists).
    *   If only `object` is provided, it fetches all tuples for that object.
    *   If only `user` is provided, it fetches all tuples for that user.
    *   If only `user` and `relation` are provided, it fetches all objects for that user with that relation.
    *   And so on for other combinations.
    *   If `tupleKey` is `null` or empty, it reads all tuples in the store (use with caution on large stores, pagination is recommended).
*   `pageSize` (optional int): The maximum number of tuples to return in a single response. Used for pagination.
*   `continuationToken` (optional string): A token received from a previous `readTuples` response, used to fetch the next page of results.
*   `consistency` (optional `OpenFGA\Enum\Consistency`): Specifies the consistency level for the read operation.
    *   `Consistency::NONE` (Default): Offers the lowest latency. Reads may not reflect the absolute latest writes due to replication delays. Generally suitable for most cases.
    *   `Consistency::SNAPSHOT`: Reads from a consistent snapshot of the data. This ensures that all results are from the same point in time, but that point might be slightly delayed from the absolute latest writes.
    *   `Consistency::STRICT`: Ensures that reads reflect all committed writes up to the point the read was initiated. This offers the highest consistency but may incur higher latency.
    *   For detailed explanations of consistency levels, refer to the [official OpenFGA documentation on Read Consistency](https://openfga.dev/docs/reference/consistency).

```php
<?php
// Example 1: Read all tuples for a specific object
try {
    $filterByObject = new TupleKey(object: 'document:roadmap');
    /** @var ReadTuplesResponseInterface $response */
    $response = unwrap($client->readTuples(tupleKey: $filterByObject, pageSize: 10));

    echo "Tuples for 'document:roadmap':\n";
    if (empty($response->getTuples())) {
        echo "No tuples found for this object.\n";
    } else {
        foreach ($response->getTuples() as $tuple) {
            echo "- User: {$tuple->getUser()}, Relation: {$tuple->getRelation()}, Object: {$tuple->getObject()}\n";
        }
    }
    // Handle pagination using $response->getContinuationToken() if needed.

} catch (Throwable $e) {
    echo "Error reading tuples by object: " . $e->getMessage() . "\n";
}

// Example 2: Read all documents where 'user:anne' is a 'viewer'
try {
    $filterAnneViewer = new TupleKey(user: 'user:anne', relation: 'viewer');
    /** @var ReadTuplesResponseInterface $response */
    $response = unwrap($client->readTuples(tupleKey: $filterAnneViewer));

    echo "\n'user:anne' is a 'viewer' of:\n";
    if (empty($response->getTuples())) {
        echo "No objects found where Anne is a viewer.\n";
    } else {
        foreach ($response->getTuples() as $tuple) {
            echo "- Object: {$tuple->getObject()}\n";
        }
    }
} catch (Throwable $e) {
    echo "Error reading tuples for Anne as viewer: " . $e->getMessage() . "\n";
}

// Example 3: Reading all tuples in the store (paginated)
$continuationToken = null;
echo "\nAll tuples in the store (paginated):\n";
try {
    do {
        /** @var ReadTuplesResponseInterface $response */
        $response = unwrap($client->readTuples(pageSize: 5, continuationToken: $continuationToken));
        foreach ($response->getTuples() as $tuple) {
            echo "- User: {$tuple->getUser()}, Relation: {$tuple->getRelation()}, Object: {$tuple->getObject()}\n";
        }
        $continuationToken = $response->getContinuationToken();
    } while (!empty($continuationToken));
} catch (Throwable $e) {
    echo "Error reading all tuples: " . $e->getMessage() . "\n";
}
?>
```

## Listing Tuple Changes (`Client::listTupleChanges()`)

Also known as "ReadChanges," the `listTupleChanges()` method allows you to track changes (writes and deletes) to relationship tuples for a specific object type over time. This is useful for auditing, data synchronization, or triggering workflows based on permission changes.

It returns a list of changes, where each change indicates whether a tuple was written or deleted, along with the tuple itself and the timestamp of the change.

**Parameters:**

*   `type` (required string): The object type you want to see changes for (e.g., `document`, `folder`). You cannot leave this empty.
*   `pageSize` (optional int): The maximum number of changes to return in a single response.
*   `continuationToken` (optional string): A token from a previous `listTupleChanges` response to fetch the next page.
*   `startTime` (optional `DateTimeInterface`): If provided, only returns changes that occurred at or after this specific point in time. Useful for fetching changes since your last check.

```php
<?php
// Example: Listing changes for the 'document' type
$continuationTokenChanges = null;
echo "\nChanges for 'document' type (paginated):\n";
try {
    // Optional: To get changes from a certain time, e.g., last hour
    // $startTime = (new \DateTimeImmutable())->sub(new \DateInterval('PT1H'));

    do {
        /** @var ListTupleChangesResponseInterface $response */
        $response = unwrap($client->listTupleChanges(
            type: 'document', // Required: specify the object type
            pageSize: 3,
            continuationToken: $continuationTokenChanges
            // startTime: $startTime // Uncomment to filter by time
        ));

        if (empty($response->getChanges())) {
            echo "No changes found for 'document' type in this page.\n";
        } else {
            foreach ($response->getChanges() as $change) {
                $tuple = $change->getTupleKey();
                echo "- Operation: " . $change->getOperation()->value . "\n"; // 'write' or 'delete'
                echo "  Tuple: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
                echo "  Timestamp: " . $change->getTimestamp()->format('Y-m-d H:i:s.u P') . "\n";
            }
        }
        $continuationTokenChanges = $response->getContinuationToken();
    } while (!empty($continuationTokenChanges));

} catch (Throwable $e) {
    echo "Error listing tuple changes: " . $e->getMessage() . "\n";
}
?>
```

## Next Steps

Now that you know how to manage relationship data by writing, reading, and listing changes to tuples, you can:

*   **[Perform Relationship Queries (Queries.md)](Queries.md):** Use methods like `check()`, `listObjects()`, and `listUsers()` to determine effective permissions based on your model and tuple data.
*   **[Write Assertions (Assertions.md)](Assertions.md):** Test your authorization model and data to ensure they behave as expected.
```
