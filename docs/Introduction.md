# Getting started

Build your first authorization system in under 10 minutes. You'll install the SDK, connect to OpenFGA, and check your first permission.

## Prerequisites

**Required:**

- PHP 8.3+ and Composer
- An OpenFGA server (local or remote)

**HTTP Libraries (auto-detected):**
The SDK will automatically discover and use existing PSR-7/17/18 libraries in your project.

If you don't have any compatible libraries installed yet, you can use Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

**Common imports for examples:**
Most examples in this guide use these imports:

```php
<?php

use OpenFGA\Client;
use function OpenFGA\{store, dsl, model, tuple, tuples, write, allowed};
```

## Start an OpenFGA server

> [!NOTE] > **Using a managed service?** Skip this step and use your Auth0 FGA or hosted OpenFGA URL instead.

The easiest way is Docker - one command gets you running:

```bash
docker run -d -p 8080:8080 --name openfga openfga/openfga run
```

Your server is now accessible at `http://localhost:8080`.

## Install the SDK

```bash
composer require evansims/openfga-php
```

## Create your first client

```php
$client = new Client(url: 'http://localhost:8080');
```

That's it. The client automatically discovers your HTTP libraries and handles all the low-level communication.

**For production:** you'll want authentication and better error handling. We'll cover that in [Authentication →](Authentication.md) and [Results →](Results.md).

## How authorization works

Every authorization system needs three things:

**1. A store**—Think of this as a database for your permissions. Each application or tenant gets its own store.

**2. A model**—The rules that define what permissions exist. Like "documents can have viewers and editors."

**3. Relationships**—Specific permissions between users and resources. Like "Alice can view document:readme."

Once you have these, you can ask questions like "Can Alice edit this document?" and get instant answers.

## Build your first authorization system

Here's a complete working example that sets up authorization for a document system:

```php
$client = new Client(url: 'http://localhost:8080');

// 1. Create a store for your app
$storeId = store($client, 'document-system');
echo "Created store: {$storeId}\n";

// 2. Define what permissions exist
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
$modelId = model($client, $storeId, $model);
echo "Created model: {$modelId}\n";

// 3. Grant Alice permission to view the readme
write(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuples: tuple('user:alice', 'viewer', 'document:readme')
);
echo "Granted alice viewer permission on readme\n";

// 4. Check if Alice can view the document
$canView = allowed(
    client: $client,
    store: $storeId,
    model: $modelId,
    tuple: tuple('user:alice', 'viewer', 'document:readme')
);

echo $canView ? "✅ Alice can view readme" : "❌ Access denied";
```

**Run this example:**

1. Make sure your OpenFGA server is running
2. Save the code as `example.php`
3. Run `php example.php`

You should see confirmation messages and a final "Alice can view readme" success message.
