# Managing Stores in OpenFGA

Think of a **Store** as your own private universe for authorization rules. Just like how you might use separate databases for different projects, OpenFGA Stores let you completely isolate authorization systems from each other.

**🏗️ What's in a Store?**

- Your authorization models (the rules about who can do what)
- Relationship tuples (the actual permission assignments)
- Assertions (tests to verify your rules work correctly)

**🤔 Why Use Multiple Stores?**

Perfect for organizing complex scenarios:

- **🌍 Multi-environment:** `myapp-dev`, `myapp-staging`, `myapp-production`
- **🏢 Multi-tenant:** `customer-acme`, `customer-globex`, `customer-initech`
- **📦 Multi-product:** `billing-service`, `user-management`, `content-platform`
- **🔒 Security isolation:** Keep different systems completely separate

**New to stores?** Start with just one for your application. You can always add more later as your needs grow.

**Already managing multiple systems?** Skip to [Creating Stores](#creating-a-store) or [Store Organization Best Practices](#store-organization-best-practices).

## Prerequisites

These examples assume:

1. You have initialized the SDK client as `$client`. If not, please refer to the [Getting Started guide](GettingStarted.md).
2. You have included necessary `use` statements for response types and helper functions.
3. The variable `$storeId` in the examples refers to the unique identifier of an OpenFGA store. You'll typically get this ID when you create a store or list existing ones.

For robust error handling beyond the `unwrap()` helper shown in these examples, please see our guide on [Results and Error Handling](Results.md). All client methods return `Result` objects that can be `Success` or `Failure`.

```php
<?php

// Make sure you have these or similar use statements at the top of your PHP file:
require_once __DIR__ . '/vendor/autoload.php'; // If running examples standalone

use OpenFGA\Client;
use OpenFGA\Responses\{CreateStoreResponseInterface, GetStoreResponseInterface, ListStoresResponseInterface, DeleteStoreResponseInterface};

use function OpenFGA\Results\unwrap;

// Assuming $client is initialized as shown in GettingStarted.md
// $client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');
?>
```

## Creating a Store

Creating a store is like setting up a new database - you give it a meaningful name, and OpenFGA returns a unique ID that you'll use for all future operations.

**💡 Naming Tips:**

- Use descriptive names: `acme-corp-production` not `store1`
- Include environment: `myapp-staging` vs `myapp-production`
- Consider your organization: `billing-service` vs `user-portal`

**⚠️ Important:** Save the store ID! You'll need it for creating models, writing tuples, and checking permissions.

```php
<?php
// Example: Creating a new store
try {
    $newStoreName = 'My Application Store';
    $store = unwrap($client->createStore(name: $newStoreName));

    echo "Store created successfully!\n";
    echo "Name: " . $store->getName() . "\n";
    echo "ID: " . $store->getId() . "\n"; // <-- This ID is very important!

    // You'll typically save this ID for later use.
    $storeId = $store->getId();

} catch (Throwable $e) {
    echo "Error creating store: " . $e->getMessage() . "\n";
}
?>
```

## Listing Stores

You can retrieve a list of all stores associated with your OpenFGA environment. This is useful for management purposes or if you need to find an existing store's ID.

The `listStores()` method supports pagination through `pageSize` and `continuationToken` parameters. For simplicity, this example fetches the first page.

```php
<?php
// Example: Listing stores
try {
    $storesResponse = unwrap($client->listStores(pageSize: 10)); // Get up to 10 stores

    echo "Available Stores:\n";
    if (empty($storesResponse->getStores())) {
        echo "No stores found.\n";
    } else {
        foreach ($storesResponse->getStores() as $store) {
            echo "- Name: " . $store->getName() . ", ID: " . $store->getId() . "\n";
        }
    }

    // For handling more stores than pageSize, you'd use $storesResponse->getContinuationToken()
    // and pass it in the next call to listStores() via the `continuationToken` parameter.
    // See API documentation for more on pagination.

} catch (Throwable $e) {
    echo "Error listing stores: " . $e->getMessage() . "\n";
}
?>
```

## Getting a Specific Store

If you have a store's ID, you can fetch its details.

```php
<?php
// Example: Getting a specific store (assuming $storeId is known)
// $storeId = 'your_known_store_id'; // Replace with an actual store ID

if (empty($storeId)) {
    echo "Please set a \$storeId to run this example.\n";
} else {
    try {
        $store = unwrap($client->getStore(store: $storeId));

        echo "Store Details:\n";
        echo "ID: " . $store->getId() . "\n";
        echo "Name: " . $store->getName() . "\n";
        echo "Created At: " . $store->getCreatedAt()->format('Y-m-d H:i:s') . "\n";
        echo "Updated At: " . $store->getUpdatedAt()->format('Y-m-d H:i:s') . "\n";

    } catch (Throwable $e) {
        // This could be an OpenFGA\Errors\StoreNotFoundError for example
        echo "Error getting store '{$storeId}': " . $e->getMessage() . "\n";
    }
}
?>
```

## Deleting a Store

You can delete a store using its ID. This action is permanent and will remove the store along with all its authorization models, tuples, and assertions. **Use with caution!**

```php
<?php
// Example: Deleting a store (assuming $storeId is known and you want to delete it)
// $storeIdToDelete = 'your_store_id_to_delete'; // Replace with an actual store ID

// For safety, let's not run delete automatically in this example.
// To actually run this, uncomment the lines and set $storeIdToDelete.
/*
if (empty($storeIdToDelete)) {
    echo "Please set a \$storeIdToDelete to run the delete example.\n";
} else {
    try {
        unwrap($client->deleteStore(store: $storeIdToDelete));
        echo "Store '{$storeIdToDelete}' deleted successfully.\n";
    } catch (Throwable $e) {
        echo "Error deleting store '{$storeIdToDelete}': " . $e->getMessage() . "\n";
    }
}
*/

echo "Delete store example is commented out for safety. Please review and uncomment to run.\n";
?>
```

## Store Organization Best Practices

**🎯 When to Use Multiple Stores**

**Use separate stores for:**

- **Different environments** (dev/staging/production) - keeps test data isolated
- **Different tenants** in SaaS apps - complete data isolation
- **Different applications** that don't share permissions
- **Compliance requirements** that mandate data separation

**Use a single store for:**

- **Different user roles** in the same app (use authorization models instead)
- **Different features** in the same product (model them as different object types)
- **Temporary testing** (just use different object IDs in your existing store)

**🏗️ Naming Conventions**

Structure your store names for easy management:

```
{product}-{environment}          → "billing-production"
{customer}-{product}             → "acme-corp-platform"
{team}-{service}-{environment}   → "auth-team-iam-staging"
```

**💡 Pro Tips:**

- **Start simple:** One store per environment is usually enough initially
- **Document ownership:** Keep track of who manages each store
- **Plan for growth:** Consider how your store strategy scales with your architecture
- **Test store switching:** Make sure your app gracefully handles store configuration changes

## Next Steps

With a Store created and selected, your next logical step is to define the rules of your authorization system within that Store. This is done by creating an **Authorization Model**.

- **[Define an Authorization Model](AuthorizationModels.md)**

You can also explore other topics:

- [Managing Relationship Tuples](RelationshipTuples.md)
- [Performing Queries (Checks, ListObjects, etc.)](Queries.md)
- [Testing with Assertions](Assertions.md)
