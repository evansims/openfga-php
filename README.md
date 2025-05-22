# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

Welcome to the OpenFGA PHP SDK! This SDK provides a high-performance and developer-friendly way to integrate your PHP applications with [OpenFGA](https://openfga.dev/) or [Auth0 FGA](https://auth0.com/fine-grained-authorization). OpenFGA is an open-source solution for fine-grained authorization, designed to give you flexibility and control over who can access what in your applications.

With this SDK, you can easily manage authorization stores, define expressive authorization models using a domain-specific language (DSL), manage relationship tuples, and perform authorization checks and queries.

## Requirements

- PHP 8.3+
- Composer
- An active OpenFGA or Auth0 FGA instance.
- PSR-7, PSR-17, and PSR-18 compatible HTTP client and factory implementations. Popular choices include Guzzle, Symfony HTTP Client, etc. These are typically auto-discovered or can be explicitly configured.

## Installation

You can install the SDK via Composer:

```bash
composer require evansims/openfga-php
```

Ensure your project also includes implementations for PSR-7, PSR-17 (HTTP Factories), and PSR-18 (HTTP Client). If not already present, Composer will often prompt you to install them, or you can add them manually. For example, to use Guzzle:

```bash
composer require guzzlehttp/guzzle # PSR-7 and PSR-18
composer require guzzlehttp/psr7 # PSR-7 and PSR-17
```

## Quick Start

Here's a quick example to get you up and running. This demonstrates creating a client, setting up a store, defining an authorization model, writing a relationship tuple, and performing an authorization check.

```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Models\TupleKey;
use OpenFGA\Models\TupleKeys;
use OpenFGA\Results\unwrap; // Helper to simplify result handling

// 1. Initialize the Client
// Assumes FGA_API_URL and FGA_STORE_NAME are set in your environment or .env file
// See docs/Authentication.md for authentication options.
$client = new Client(url: $_ENV['FGA_API_URL'] ?? 'http://localhost:8080');

try {
    // 2. Create a Store (or use an existing one)
    // Stores are isolated environments for your authorization models and data.
    $storeName = $_ENV['FGA_STORE_NAME'] ?? 'my_php_sdk_store';
    $store = unwrap($client->createStore(name: $storeName));
    echo "Store '{$store->getName()}' created with ID: {$store->getId()}\n";
    $client->setStore($store->getId()); // Set store for subsequent client calls

    // 3. Define and Create an Authorization Model (DSL)
    // Authorization models define your access control structure.
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
    $client->setModel($createdModel->getId()); // Set model for subsequent client calls

    // 4. Write a Relationship Tuple
    // Tuples define relationships, e.g., "user:anne is a viewer of document:roadmap".
    $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:roadmap');
    unwrap($client->writeTuples(writes: new TupleKeys([$tuple])));
    echo "Tuple written: user:anne is a viewer of document:roadmap\n";

    // 5. Perform an Authorization Check
    // "Can user:anne view document:roadmap?"
    $checkResult = unwrap($client->check(tupleKey: $tuple));
    if ($checkResult->getIsAllowed()) {
        echo "SUCCESS: Anne CAN view the roadmap!\n";
    } else {
        echo "FAILURE: Anne CANNOT view the roadmap.\n";
    }

} catch (Throwable $e) {
    echo "An error occurred: {$e->getMessage()}\n";
    // For production, log the error and handle it gracefully.
    // You can inspect $e for more details, e.g., if it's an OpenFGA\Errors\FGAApiError
}

// For a more detailed walkthrough, see docs/GettingStarted.md
?>
```

This example uses `unwrap()` for brevity. For robust error handling, explore the `Success`/`Failure` patterns described in [Error Handling and Results](docs/Results.md).

## Core Concepts

This SDK allows you to interact with all the core features of OpenFGA. Here's a brief overview:

-   **Stores:** Isolated environments for your authorization models and relationship data. They allow you to manage permissions separately for different applications or tenants.
    [Learn more in `docs/Stores.md`](docs/Stores.md)

-   **Authorization Models:** These define the structure of your access control system – the types of objects, the possible relationships (e.g., `viewer`, `editor`), and how those relationships grant permissions. You can define models using a user-friendly DSL.
    [Learn more in `docs/AuthorizationModels.md`](docs/AuthorizationModels.md)

-   **Relationship Tuples:** These are the actual pieces of data that define who has what relationship with which object (e.g., "user:anne is a `viewer` of document:roadmap"). You can write, read, and delete these tuples.
    [Learn more in `docs/RelationshipTuples.md`](docs/RelationshipTuples.md)

-   **Queries:** OpenFGA allows you to ask powerful questions about your authorization data:
    -   `check()`: "Does this user have this permission on this object?"
    -   `expand()`: "How does this user have this permission?" (shows the tree of relationships)
    -   `listUsers()`: "Which users have this permission on this object?"
    -   `listObjects()`: "Which objects of a certain type can this user access with this permission?"
    [Learn more in `docs/Queries.md`](docs/Queries.md)

-   **Assertions:** These are used for testing your authorization models. You can assert whether specific relationships should exist or not, helping you verify your model's correctness.
    [Learn more in `docs/Assertions.md`](docs/Assertions.md)

## Authentication

The SDK supports various authentication methods to connect to your OpenFGA instance:
-   No authentication (suitable for local development or trusted environments).
-   API Key (Pre-Shared Key).
-   Client Credentials.

[Learn more about configuring authentication in `docs/Authentication.md`](docs/Authentication.md)

## Error Handling and Results

Client methods return `Result` objects which can be either a `Success` or a `Failure`. This pattern allows for explicit error handling. Helper functions like `unwrap()`, `fold()`, `success()`, and `failure()` are provided to work with these results.

[Learn more about handling results and errors in `docs/Results.md`](docs/Results.md)

## Documentation

For more detailed information, please refer to the following resources:

-   **SDK Documentation:** The `docs/` directory in this repository contains detailed information on:
    -   [Getting Started](docs/GettingStarted.md)
    -   [Authentication](docs/Authentication.md)
    -   [Stores](docs/Stores.md)
    -   [Authorization Models](docs/AuthorizationModels.md)
    -   [Relationship Tuples](docs/RelationshipTuples.md)
    -   [Queries](docs/Queries.md)
    -   [Assertions](docs/Assertions.md)
    -   [Results (Error Handling)](docs/Results.md)
-   **SDK API Technical Reference:** [Generated API documentation](docs/API) (link might need to be updated based on actual generation path).
-   **OpenFGA Official Documentation:** [openfga.dev/docs](https://openfga.dev/docs) for comprehensive information about OpenFGA itself.

## Contributing

Contributions are welcome! Whether it's bug reports, feature suggestions, or pull requests, please feel free to contribute.
-   If you find a bug or have a feature request, please open an issue on GitHub.
-   If you'd like to contribute code, please fork the repository and submit a pull request.

(Consider adding a `CONTRIBUTING.md` file with more detailed guidelines if one doesn't exist).

## License

This project is licensed under the Apache 2.0 License. See the [LICENSE](LICENSE) file for details.

---

This project is an unofficial, community-maintained SDK and is not endorsed by OpenFGA or Auth0.
