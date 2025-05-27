# Managing Stores in OpenFGA

In OpenFGA, a **Store** is a fundamental concept. Think of it as a top-level container or an isolated database dedicated to your authorization logic. Each Store holds its own set of authorization models, relationship tuples (the data defining permissions), and assertions.

**Why are Stores important?**

- **Isolation:** They allow you to segregate authorization systems for different applications, services, or tenants. For example, you might have separate stores for `MyApp_Development`, `MyApp_Staging`, and `MyApp_Production`.
- **Organization:** They help keep your authorization configurations clean and manageable, especially as your system grows.
- **Multi-tenancy:** If you're building a multi-tenant application, you could potentially use a store per tenant to keep their authorization rules and data completely separate.

This guide will show you how to manage Stores using the OpenFGA PHP SDK.

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

When you create a store, you give it a name. OpenFGA will assign it a unique `id`. This `id` is crucial as you'll use it for almost all other operations related to this store, like adding authorization models or writing tuples.

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

## Using a Specific Store for Operations

Once you have a `storeId` (either from creating a new store or by listing/getting an existing one), you need to tell the OpenFGA client which store you intend to operate on for subsequent calls like managing authorization models, writing tuples, or performing checks.

You can do this using the `Client::setStore(string $storeId)` method.

```php
<?php
// Example: Setting the active store on the client
// $storeId = 'your_active_store_id'; // Replace with your actual store ID

if (empty($storeId)) {
    echo "Please set a \$storeId to run this example.\n";
} else {
    try {
        $client->setStore($storeId); // Crucial step!
        echo "Client is now configured to use Store ID: {$storeId}\n";

        // Now, any calls like $client->createAuthorizationModel(...) or $client->check(...)
        // will be performed within the context of this store.
        // You can also override this on a per-call basis if needed,
        // but setting it on the client is often more convenient.

    } catch (Throwable $e) { // Though setStore itself doesn't throw, operations using it might.
        echo "Error in operations after setting store '{$storeId}': " . $e->getMessage() . "\n";
    }
}
?>
```

If you don't set a store ID on the client, you'll need to pass the `store` parameter in every relevant SDK method call. Setting it on the client simplifies your code if you're working primarily with one store at a time.

## Next Steps

With a Store created and selected, your next logical step is to define the rules of your authorization system within that Store. This is done by creating an **Authorization Model**.

- **[Define an Authorization Model](AuthorizationModels.md)**

You can also explore other topics:

- [Managing Relationship Tuples](RelationshipTuples.md)
- [Performing Queries (Checks, ListObjects, etc.)](Queries.md)
- [Testing with Assertions](Assertions.md)
