# Stores

Think of a store as your authorization workspace. It contains your permission rules, user relationships, and everything needed to answer "can this user do that?" Each store is completely isolated - perfect for separating environments, tenants, or applications.

Every OpenFGA operation happens within a store, making them the foundation of your authorization system.

## Prerequisites

The examples in this guide assume you have the following setup:

```php
use OpenFGA\Client;
use function OpenFGA\store;

// Initialize your client
$client = new Client(url: $_ENV['FGA_API_URL']);
```

## What are stores

A store holds three things:

- **Authorization models** - your permission rules
- **Relationship tuples** - who can do what
- **Assertions** - tests to verify everything works

Most apps start with one store and add more as they grow.

## Single application setup

For a typical application, create one store per environment:

```php
// Create your production store
$storeId = store($client, 'myapp-production'); // Save this!

// Configure your client to use this store
$client = $client->withStore(store: $storeId);
```

Store that ID in your environment configuration - you'll need it for every API call.

## Multi-tenant patterns

For SaaS applications, create a store per customer to ensure complete data isolation:

```php
final readonly class TenantStoreManager
{
    public function __construct(private Client $client) {}

    public function createTenantStore(string $customerId): string
    {
        $store = $this->client
            ->createStore(name: "customer-{$customerId}")
            ->unwrap();

        return $store->getId();
    }

    public function getClientForTenant(string $customerId): Client
    {
        $storeId = $this->lookupStoreId($customerId);
        return $this->client->withStore(store: $storeId);
    }
}

// Usage
$manager = new TenantStoreManager($client);
$storeId = $manager->createTenantStore('acme-corp');
```

## Environment separation

Keep your environments completely isolated:

```php
enum Environment: string
{
    case Development = 'dev';
    case Staging = 'staging';
    case Production = 'prod';
}

function createEnvironmentStore(Client $client, Environment $env, string $appName): string
{
    $store = $client->createStore(name: "{$appName}-{$env->value}")->unwrap();
    return $store->getId();
}

// Create stores for each environment
$devStoreId = createEnvironmentStore($client, Environment::Development, 'myapp');
$prodStoreId = createEnvironmentStore($client, Environment::Production, 'myapp');
```

## Store management

Finding and managing existing stores:

```php
// List all stores
$stores = $client->listStores(pageSize: 20)->unwrap();
foreach ($stores->getStores() as $store) {
    echo "{$store->getName()}: {$store->getId()}\n";
}

// Get specific store details (using store ID from previous examples)
$store = $client->getStore(store: $storeId)->unwrap();
echo "Created: {$store->getCreatedAt()->format('Y-m-d H:i:s')}\n";

// Delete a store (careful - this is permanent!)
$client->deleteStore(store: $storeId)->unwrap();
```

For pagination with many stores:

```php
$continuationToken = null;
do {
    $response = $client->listStores(
        pageSize: 10,
        continuationToken: $continuationToken
    )->unwrap();

    foreach ($response->getStores() as $store) {
        // Process each store
    }

    $continuationToken = $response->getContinuationToken();
} while ($continuationToken !== null);
```

## Best practices

**When to use multiple stores:**

- Different environments (dev/staging/production)
- Different customers in SaaS apps
- Different applications with no shared permissions
- Compliance requirements for data isolation

**When to use a single store:**

- Different user roles (use authorization models instead)
- Different features in the same app (use object types)
- A/B testing (use different object IDs)

**Naming conventions:**

```php
// Good names
'myapp-production'
'customer-acme-corp'
'billing-service-staging'

// Avoid
'store1'
'test'
'temp'
```

**Pro tips:**

- Start with one store per environment
- Save store IDs in your configuration
- Test your app works with store switching
- Document which team owns each store

## Next steps

With your store ready, create an [Authorization Model](Models.md) to define your permission rules.
