**Stores can be thought of as your authorization database.** They contain your permission rules, user relationships, and everything needed to answer "can this user do that?" Every OpenFGA operation happens within a store, making them the foundation of your authorization system.

## Prerequisites

The examples in this guide assume you have the following setup:

[Snippet](../../examples/snippets/stores-setup.php)

## Single application setup

For a typical application, create one store per environment:

[Snippet](../../examples/snippets/stores-basic.php#usage)

Save the store ID in your environment configuration. You'll need it for future API calls.

## Multi-tenant patterns

For SaaS applications, create a store per customer to ensure complete data isolation:

[Snippet](../../examples/snippets/stores-multi-tenant.php#usage)

## Environment separation

Keep your environments completely isolated:

[Snippet](../../examples/snippets/stores-management.php#seperation)

## Store management

Finding and managing existing stores:

[Snippet](../../examples/snippets/stores-management.php#management)

For pagination with many stores:

[Snippet](../../examples/snippets/stores-management.php#pagination)

## Best practices

**When to use multiple stores:**

- Different environments (dev/staging/production)
- Different customers in SaaS apps
- Different applications with no shared permissions
- Compliance requirements

**When to use a single store:**

- Different user roles (use authorization models instead)
- Different features in the same app (use object types)
- A/B testing (use different object IDs)

**Pro tips:**

- Start with one store per environment
- Save store IDs in your configuration
- Test your app works with store switching
- Document which team owns each store
