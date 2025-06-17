**Queries are how your application enforces access control.** With an [authorization model](Models.md) and [relationship tuples](Tuples.md) in place, it's time to put them to use.

## Prerequisites

Before diving into the examples, make sure you have the necessary setup:

[Snippet](../../examples/snippets/queries-setup.php)

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

[Snippet](../../examples/snippets/queries-check.php#helper)

> The `allowed` helper wraps the Client `check` method, and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors and return `false` if the request fails.

<details>
<summary>Use the Client <code>check</code> method if you need more control over the operation…</summary>

[Snippet](../../examples/snippets/queries-check.php#client)

</details>

#### Check multiple permissions at once

Use the `checks` helper to check multiple permissions at once:

[Snippet](../../examples/snippets/queries-batch-check.php#helper)

> The `checks` helper wraps the Client `batchCheck` method, and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>batchCheck</code> method directly if you need more control over the operation…</summary>

[Snippet](../../examples/snippets/queries-batch-check.php#client)

</details>

### List accessible objects

Use the `objects` helper to retrieve a list of objects a user can access.

[Snippet](../../examples/snippets/queries-list-objects.php#helper)

> The `objects` helper wraps the Client `streamedListObjects` method and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>streamedListObjects</code> or <code>listObjects</code> methods directly if you need more control over the operation…</summary>

[Snippet](../../examples/snippets/queries-list-objects.php#client)

</details>

### Find users with permission

Use the `users` helper to retrieve a list of users who have a specific permission on an object.

[Snippet](../../examples/snippets/queries-list-users.php#helper)

> The `users` helper wraps the Client `listUsers` method and is intended for situations where graceful degradation is preferred over exception handling. It will silently ignore errors.

<details>
<summary>Use the Client <code>listUsers</code> method directly if you need more control over the operation…</summary>

[Snippet](../../examples/snippets/queries-list-users.php#client)

</details>

### Expand relationships

When permissions aren't working as expected, use the Client `expand` method to discover why. It returns the complete relationship tree, and is useful for debugging complex permission structures or understanding why a user has (or doesn't have) access.

[Snippet](../../examples/snippets/queries-expand.php#client)

## Advanced patterns

### Contextual tuples

Test "what-if" scenarios without permanently saving relationships. Perfect for previewing permission changes.

[Snippet](../../examples/snippets/queries-contextual.php#client)

### Consistency levels

For read-after-write scenarios, you might need stronger consistency:

[Snippet](../../examples/snippets/queries-consistency.php#consistency)

#### Advanced error handling

Use enum-based exceptions for more precise error handling with i18n support:

[Snippet](../../examples/snippets/queries-advanced.php#error-handling)

## Common Query Patterns

### Permission gates for routes

[Snippet](../../examples/snippets/queries-advanced.php#permission-gates)

### Efficient data filtering

[Snippet](../../examples/snippets/queries-advanced.php#data-filtering)

### Debugging permission issues

[Snippet](../../examples/snippets/queries-advanced.php#debugging)
