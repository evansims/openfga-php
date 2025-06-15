This guide takes you through building your first authorization. You'll install the SDK, connect to OpenFGA, and check your first permission.

## Prerequisites

- PHP 8.3+ and Composer
- An [OpenFGA](https://openfga.dev) server (local or remote)
- [PSR-7](https://packagist.org/providers/psr/http-message-implementation), [PSR-17](https://packagist.org/providers/psr/http-factory-implementation) and [PSR-18](https://packagist.org/providers/psr/http-client-implementation) implementations

## Anatomy of Authorization

Authorization with OpenFGA boils down to three things:

- A [store](..%2FEssentials%2FStores.md), which acts like a database for your permissions.
- An [authorization model](..%2FEssentials%2FModels.md), which defines what permissions exist. Like "documents can have viewers and editors."
- The [relationship tuples](..%2FEssentials%2FTuples.md), which establish specific permissions between users and resources. Like "Alice can view document:readme."

With those elements in place, you can then use [permission queries](..%2FEssentials%2FQueries.md) to have OpenFGA answer questions like "can Alice edit this document?"

## Quickstart

### 1. Start OpenFGA

The quickest method is Docker — one command gets you up and running:

```bash
docker run -d -p 8080:8080 --name openfga openfga/openfga run
```

Your server is now accessible at `http://localhost:8080`.

### 2. Install the SDK

Use Composer to add the SDK to your application:

```bash
composer require evansims/openfga-php
```

In some cases you may need to [install additional dependencies](Installation.md) for the SDK.

### 3. Integrate the SDK

#### 3.1. Create a Client

Begin integrating the SDK by initializing an SDK Client in your application:

```php
use OpenFGA\Client;

$client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');
```

In a production environment you'll want [authentication](Authentication.md) and [error handling](Results.md).

#### 3.2. Create a Store

```php
use function OpenFGA\store;

$storeId = store($client, 'example-document-system');
echo "Created store: {$storeId}\n";
```

#### 3.3 Define a Model

```php
use function OpenFGA\dsl;

$dsl = <<<DSL
model
  schema 1.1

type user

type document
  relations
    define viewer: [user]
    define editor: [user]
DSL;

$model = dsl($client, $dsl);
```

#### 3.4 Create a Model

```php
use function OpenFGA\model;

$modelId = model($client, $storeId, $model);

echo "Created model: {$modelId}\n";
```

#### 3.5 Grant Permission

```php
use function OpenFGA\{write, tuple};

write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:alice', 'viewer', 'document:readme')
);

echo "Granted alice viewer permission on readme\n";
```

#### 3.6 Check Permission

```php
use function OpenFGA\{allowed, tuple};

$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:alice', 'viewer', 'document:readme')
);

echo $canView ? "✅ Alice can view readme" : "❌ Access denied";
```

### 4. **Run the Example**

1. Make sure your OpenFGA server is running
2. Save the code as `example.php`
3. Run `php example.php`
