# Getting Started with the OpenFGA PHP SDK

Welcome! This guide will walk you through the initial steps to integrate the OpenFGA PHP SDK into your project, enabling you to add powerful fine-grained authorization to your application. We'll cover setting up your environment, installing the SDK, initializing the client, and performing your first authorization check.

Our goal is to get you up and running as quickly as possible.

## Prerequisites

Before you begin, ensure you have the following:

- **PHP:** Version 8.3 or higher.
- **Composer:** For managing PHP dependencies.
- **OpenFGA Instance:**
  - For local development, an OpenFGA server instance. [Docker](https://openfga.dev/docs/getting-started/setup-openfga/docker) is recommended.
  - Alternatively, an [Auth0 FGA](https://auth0.com/fine-grained-authorization) account (managed service).
- **PSR Implementations:** Your project must include implementations for:
  - [PSR-7 (HTTP Message Interfaces)](https://packagist.org/providers/psr/http-message-implementation)
  - [PSR-17 (HTTP Factory Interfaces)](https://packagist.org/providers/psr/http-factory-implementation)
  - [PSR-18 (HTTP Client Interfaces)](https://packagist.org/providers/psr/http-client-implementation)
    (Refer to the main [README.md requirements section](../../README.md#requirements) for more details and installation examples, e.g., using Guzzle.)

## 1. Setting up OpenFGA

You need a running OpenFGA server to connect to.

**For Local Development (using Docker):**

This is the quickest way to get an OpenFGA server running locally.

```bash
# Pull the latest OpenFGA image
docker pull openfga/openfga

# Run the OpenFGA server
docker run -d -p 8080:8080 --name openfga openfga/openfga run
```

This will start an OpenFGA server accessible at `http://localhost:8080`.

**For Auth0 FGA (Managed Service):**

If you're using Auth0 FGA, follow the setup instructions provided in your Auth0 dashboard to get your API URL and necessary credentials.

## 2. Installing the SDK

Install the OpenFGA PHP SDK into your project using Composer:

```bash
composer require evansims/openfga-php
```

Remember to also install your chosen PSR-7, PSR-17, and PSR-18 implementations if they are not already part of your project. For example, if you want to use Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

## 3. Initializing the Client

Once the SDK is installed, you can initialize the `OpenFGA\Client`.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php'; // Adjust path if necessary

use OpenFGA\Client;

// Configure the client with your OpenFGA API URL
// It's good practice to use environment variables for configuration
$fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080'; // Default for local Docker

$client = new Client(url: $fgaApiUrl);

// Authentication:
// By default, the client uses no authentication.
// For production, you'll likely use API Keys or Client Credentials.
// See Authentication.md for detailed authentication setup.

// Result Handling:
// SDK methods return Result objects (Success or Failure).
// See Results.md to learn about robust error handling.
?>
```

Ensure your `FGA_API_URL` environment variable is set correctly, or directly replace the URL if you're testing locally.

## 4. Core Authorization Workflow Overview

With the client initialized, the typical workflow in OpenFGA involves these steps:

1. **Create a Store:** Stores are containers that isolate your authorization models and relationship data. This is often a one-time setup per application or tenant.

   - [Learn more about Stores](Stores.md)

2. **Define an Authorization Model:** Within a store, you'll define an authorization model using the OpenFGA DSL. This model specifies your object types (e.g., `document`, `folder`), the relationships they can have (e.g., `viewer`, `editor`), and how these relationships grant permissions.

   - [Learn more about Authorization Models](AuthorizationModels.md)

3. **Write Relationship Tuples:** Tuples are statements that define specific relationships between users (or other subjects) and objects. For example: "`user:anne` is a `viewer` of `document:roadmap`".

   - [Learn more about Relationship Tuples](RelationshipTuples.md)

4. **Perform Queries:** Once your model and tuples are in place, you can ask OpenFGA questions:

   - `check()`: "Does user X have Y permission on object Z?"
   - `listObjects()`: "What objects can user X access with Y permission?"
   - `listUsers()`: "Which users have Y permission on object Z?"
   - `expand()`: "How (via which relationships) does user X have Y permission on object Z?"
   - [Learn more about Queries](Queries.md)

5. **(Optional) Test with Assertions:** You can write assertions to test your authorization model and ensure it behaves as expected.
   - [Learn more about Assertions](Assertions.md)

## 5. Your First End-to-End Check

Let's put some of the core concepts together in a simple, runnable example. This demonstrates creating a store, defining a model, writing a tuple, and performing a check.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php'; // Adjust if your vendor dir is elsewhere

use OpenFGA\Client;
use OpenFGA\Models\AuthorizationModel;
use OpenFGA\Responses\{
    CreateStoreResponseInterface,
    CreateAuthorizationModelResponseInterface,
    WriteTuplesResponseInterface,
    CheckResponseInterface
};
use function OpenFGA\Models\{tuple, tuples};
use function OpenFGA\Results\{unwrap};

// Initialize the client (ensure FGA_API_URL is set or use default)
$fgaApiUrl = $_ENV['FGA_API_URL'] ?? 'http://localhost:8080';
$client = new Client(url: $fgaApiUrl);

try {
    // 1. Create a Store
    // For this example, we'll use a unique name to avoid conflicts.
    $storeName = 'my_first_fga_store_' . bin2hex(random_bytes(3));
    $store = unwrap($client->createStore(name: $storeName));
    echo "Store '{$store->getName()}' created with ID: {$store->getId()}\n";

    // Configure the client to use this store for subsequent calls
    $client->setStore($store->getId());

    // 2. Define and Create an Authorization Model (DSL)
    $dsl = <<<DSL
    model
      schema 1.1
    type user
    type document
      relations
        define viewer: [user]
    DSL;
    $model = unwrap($client->dsl($dsl)); // Transform DSL to model object
    $createdModel = unwrap($client->createAuthorizationModel(
        typeDefinitions: $model->getTypeDefinitions()
    ));
    echo "Authorization Model created with ID: {$createdModel->getId()}\n";

    // Configure the client to use this model for subsequent calls
    $client->setModel($createdModel->getId());

    // 3. Write a Relationship Tuple
    // "user:anne is a viewer of document:readme"
    $tuple = tuple(user: 'user:anne', relation: 'viewer', object: 'document:readme');
    unwrap($client->writeTuples(store: $store->getId(), model: $createdModel->getId(), writes: tuples($tuple)));
    echo "Tuple written: user:anne is a viewer of document:readme\n";

    // 4. Perform an Authorization Check
    // "Can user:anne view document:readme?"
    $checkResult = unwrap($client->check(store: $store->getId(), model: $createdModel->getId(), tupleKey: $tuple));

    if ($checkResult->getIsAllowed()) {
        echo "SUCCESS: Anne CAN view document:readme!\n";
    } else {
        echo "FAILURE: Anne CANNOT view document:readme. (This shouldn't happen with this setup)\n";
    }

} catch (Throwable $e) {
    echo "An error occurred: {$e->getMessage()}\n";
    // In a real application, log this error and handle it gracefully.
    // You might want to check if $e is an instance of OpenFGA\Errors\FGAApiError for specific FGA errors.
}

// Note: For a production application, you would typically create the store and model
// less frequently (perhaps during deployment or setup) and mostly perform writes and checks.
// This example uses unwrap() for brevity. See Results.md for robust error handling.
?>
```

To run this:

1. Save it as a PHP file (e.g., `test_fga.php`) in your project root (or adjust paths).
2. Ensure your OpenFGA server is running (see Step 1).
3. Execute it from your terminal: `php test_fga.php`.

You should see output indicating the successful creation of the store, model, tuple, and a successful authorization check.

## Next Steps

Now that you have a basic understanding of the SDK and have performed your first check, you can dive deeper into specific areas:

- **[Stores (`Stores.md`)](Stores.md):** Learn more about managing multiple stores.
- **[Authorization Models (`AuthorizationModels.md`)](AuthorizationModels.md):** Explore the details of the DSL and model management.
- **[Relationship Tuples (`RelationshipTuples.md`)](RelationshipTuples.md):** Understand how to read, write, and delete tuples at scale.
- **[Queries (`Queries.md`)](Queries.md):** Master the different types of authorization queries.
- **[Assertions (`Assertions.md`)](Assertions.md):** Learn how to test your authorization models effectively.
- **[Authentication (`Authentication.md`)](Authentication.md):** Secure your connection to the OpenFGA server.
- **[Results & Error Handling (`Results.md`)](Results.md):** Implement robust error handling for production applications.

Refer to the main [README.md](../../README.md) for a general overview and other information. Happy coding!
