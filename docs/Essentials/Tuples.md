**Relationship tuples are where the rubber meets the road.** They're the actual permissions in your system - they define who can do what to which resource.

## Prerequisites

The examples in this guide assume you have the following setup:

[Snippet](../../examples/snippets/tuples-setup.php)

## Granting permissions

Use the `write` helper to give someone access:

[Snippet](../../examples/snippets/tuples-basic.php#write)

## Removing permissions

Use the `delete` helper to take away access:

[Snippet](../../examples/snippets/tuples-basic.php#delete)

## Bulk operations

Use the `writes` helper to handle multiple permission changes in one transaction:

[Snippet](../../examples/snippets/tuples-bulk.php#helper)

## Reading existing permissions

Use the `read` [helper](../Features/Helpers.md) to check what permissions exist:

[Snippet](../../examples/snippets/tuples-reading.php#helper)

Alternatively, use the Client's `readTuples` method for more control:

[Snippet](../../examples/snippets/tuples-reading.php#client)

## Advanced patterns

### Conditional tuples

Use conditions to make permissions context-dependent:

[Snippet](../../examples/snippets/tuples-conditions.php#write)

[Snippet](../../examples/snippets/tuples-conditions.php#check)

### Auditing changes

Monitor permission changes over time for auditing:

[Snippet](../../examples/snippets/tuples-auditing.php#auditing)

### Working with groups

Use the `write` helper to grant permissions to groups instead of individual users:

[Snippet](../../examples/snippets/tuples-groups.php#groups)

Now Anne can edit the technical specs because she's a member of the engineering team.

For checking permissions and querying relationships, see [Queries](Queries.md).

## Error handling with tuples

The SDK has a powerful enum-based exception handling system that allows you to handle errors in a type-safe way.

[Snippet](../../examples/snippets/tuples-error-handling.php#error-handling)

### Supporting multiple languages

The error messages from tuple operations will automatically use the language configured in your client:

[Snippet](../../examples/snippets/tuples-multilang.php)

## What's next

After writing tuples to grant permissions, you'll want to verify those permissions are working correctly. The [Queries](Queries.md) guide covers how to check permissions, list user access, and discover relationships using the tuples you've created.
