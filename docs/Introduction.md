# Getting started

Build your first authorization system in under 10 minutes. You'll install the SDK, connect to OpenFGA, and check your first permission.

## What you'll learn

By the end of this guide, you'll have a working authorization system that can answer questions like "Can Alice view this document?" and understand the core concepts that power modern permission systems.

## Prerequisites

You'll need PHP 8.3+ and Composer. The SDK handles HTTP communication through PSR interfaces, so you'll also need HTTP libraries:

```bash
# If you don't have HTTP libraries yet
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

## Start an OpenFGA server

The easiest way is Docker - one command gets you running:

```bash
docker run -d -p 8080:8080 --name openfga openfga/openfga run
```

Your server is now accessible at `http://localhost:8080`.

**Using a managed service?** Skip this step and use your Auth0 FGA or hosted OpenFGA URL instead.

## Install the SDK

```bash
composer require evansims/openfga-php
```

## Create your first client

```php
use OpenFGA\Client;

$client = new Client(url: 'http://localhost:8080');
```

That's it. The client automatically discovers your HTTP libraries and handles all the low-level communication.

**For production:** You'll want authentication and better error handling. We'll cover that in [Authentication →](Authentication.md) and [Results →](Results.md).

## How authorization works

Every authorization system needs three things:

**1. A store** — Think of this as a database for your permissions. Each application or tenant gets its own store.

**2. A model** — The rules that define what permissions exist. Like "documents can have viewers and editors."

**3. Relationships** — Specific permissions between users and resources. Like "Alice can view document:readme."

Once you have these, you can ask questions like "Can Alice edit this document?" and get instant answers.

## Build your first authorization system

Here's a complete working example that sets up authorization for a document system:

```php
use OpenFGA\Client;
use function OpenFGA\Models\{tuple, tuples};
use function OpenFGA\Results\unwrap;

$client = new Client(url: 'http://localhost:8080');

// 1. Create a store for your app
$store = unwrap($client->createStore(name: 'document-system'));
echo "Created store: {$store->getId()}\n";

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

$model = unwrap($client->dsl($dsl));
$createdModel = unwrap($client->createAuthorizationModel(
    store: $store->getId(),
    typeDefinitions: $model->getTypeDefinitions()
));
echo "Created model: {$createdModel->getId()}\n";

// 3. Grant Alice permission to view the readme
unwrap($client->writeTuples(
    store: $store->getId(),
    model: $createdModel->getId(),
    writes: tuples(
        tuple(user: 'user:alice', relation: 'viewer', object: 'document:readme')
    )
));
echo "Granted alice viewer permission on readme\n";

// 4. Check if Alice can view the document
$result = unwrap($client->check(
    store: $store->getId(),
    model: $createdModel->getId(),
    tupleKey: tuple(user: 'user:alice', relation: 'viewer', object: 'document:readme')
));

echo $result->getIsAllowed() ? "✅ Alice can view readme" : "❌ Access denied";
```

**Run this example:**

1. Make sure your OpenFGA server is running
2. Save the code as `example.php` 
3. Run `php example.php`

You should see confirmation messages and a final "Alice can view readme" success message.

## What's next?

Now that you have a working authorization system, here's how to level up:

### Core concepts → Authorization models
Learn to build complex permission systems with inheritance, conditions, and multi-tenant patterns.
**[Authorization Models →](Models.md)**

### Querying → Permission checks  
Master the four query types: check permissions, list accessible objects, find users, and expand relationships.
**[Queries →](Queries.md)**

### Configuration → Error handling
Build production-ready apps with proper error handling using the Result pattern.
**[Results →](Results.md)**

### Authentication → Production setup
Secure your OpenFGA connection with API keys and client credentials.
**[Authentication →](Authentication.md)**

**Building something complex?** Check out [Relationship Tuples →](Tuples.md) for managing permissions at scale, or [Observability →](Observability.md) for observability.
