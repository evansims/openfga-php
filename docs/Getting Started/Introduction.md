This guide takes you through building your first authorization. You'll install the SDK, connect to OpenFGA, and check your first permission.

## Prerequisites

- PHP 8.3+ and Composer
- An [OpenFGA](https://openfga.dev) server (local or remote)
- [PSR-7](https://packagist.org/providers/psr/http-message-implementation), [PSR-17](https://packagist.org/providers/psr/http-factory-implementation) and [PSR-18](https://packagist.org/providers/psr/http-client-implementation) dependencies

## Anatomy of Authorization

Authorization with OpenFGA boils down to three things:

- A [store](../Essentials/Stores.md), which acts like a database for your permissions.
- An [authorization model](../Essentials/Models.md), which defines what permissions exist. Like "documents can have viewers and editors."
- The [relationship tuples](../Essentials/Tuples.md), which establish specific permissions between users and resources. Like "Alice can view document:readme."

With those elements in place, you can then use [permission queries](../Essentials/Queries.md) to have OpenFGA answer questions like "can Alice edit this document?"

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

[Snippet](../../examples/snippets/introduction-quickstart.php#intro)

In a production environment you'll want [authentication](Authentication.md) and [error handling](Results.md).

#### 3.2. Create a Store

[Snippet](../../examples/snippets/introduction-quickstart.php#create-a-store)

#### 3.3 Define a Model

[Snippet](../../examples/snippets/introduction-quickstart.php#define-a-model)

#### 3.4 Create a Model

[Snippet](../../examples/snippets/introduction-quickstart.php#create-a-model)

#### 3.5 Grant Permission

[Snippet](../../examples/snippets/introduction-quickstart.php#grant-permission)

#### 3.6 Check Permission

[Snippet](../../examples/snippets/introduction-quickstart.php#check-permission)

### 4. **Run the Example**

1. Make sure your OpenFGA server is running
2. Save the code as `example.php`
3. Run `php example.php`
