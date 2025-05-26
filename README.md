# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

**The OpenFGA PHP SDK puts relationship-based access control at your fingertips.** Describe “who can do what” once in a human-friendly DSL, then let [OpenFGA](https://openfga.dev/) or [Auth0 FGA](https://auth0.com/fga) enforce it. No scattering of `if` statements across your codebase or brittle policy files. A fluent API with strict typing, and PSR-compatible networking makes all the plumbing disappear, unburdening you with cleaner code, sharper security boundaries, and room to grow.

- **Strictly-typed API** — Native type hinting and linter-friendly generics.
- **Standards first** — Built for interoperability and minimal dependency overhead.
- **Human-friendly DSL** — Parse and commit authorization model changes using plain text.
- **Ready to scale** — One solution for a weekend project or an enterprise app with millions of tuples.
- **Community-maintained** — Stewarded by seasoned PHP contributors; dedicated to stability and security.

## Requirements

- PHP 8.3+
- Composer
- [OpenFGA](https://openfga.dev/) or [Auth0 FGA](https://auth0.com/fga)
- PSR-7, PSR-17, and PSR-18 networking libraries

## Installation

You can install the SDK via Composer:

```bash
composer require evansims/openfga-php
```

Composer may require you to install a PSR-7, PSR-17 (HTTP Factories), and PSR-18 (HTTP Client) implementation. Any libraries you prefer to use are fine, as long as they implement those interfaces. For example, to use Guzzle:

```bash
composer require guzzlehttp/guzzle guzzlehttp/psr7
```

## Quick Start

Here's a quick example to get you up and running. This demonstrates creating a client, setting up a store, defining an authorization model, writing a relationship tuple, and performing an authorization check.

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Models\{AuthorizationModel, tuple, tuples};
use OpenFGA\Responses\{
    CreateStoreResponseInterface,
    CreateAuthorizationModelResponseInterface
};

use function OpenFGA\Results\{success, failure, unwrap};

define('STORE_NAME', 'my-php-store');

/**
 * 1. Initialize the SDK Client
 * ----------------------------
 * The only required parameter is the URL of your OpenFGA server.
 * See docs/Authentication.md for authentication options.
 */

$client = new Client(
    url: 'http://localhost:8080',
);

/**
 * 2. Create a Store
 */

$store = ($client->createStore(name: STORE_NAME))
    ->failure(fn(Throwable $error) => throw $error)
    ->then(fn(CreateStoreResponseInterface $store) => $store->getId())
    ->success(fn($id) => print "Store created! ID: {$id}\n")
    ->unwrap();

/**
 * 3. Create an Authorization Model from a DSL
 */

$dsl = <<<DSL
    model
        schema 1.1
    type user
    type document
        relations
        define viewer: [user]
DSL;

$model = ($client->dsl($dsl))
    ->failure(fn(Throwable $error) => throw $error)
    ->then(fn(AuthorizationModel $model) => $client->createAuthorizationModel(
        store: $store,
        typeDefinitions: $model->getTypeDefinitions(),
        conditions: $model->getConditions(),
    ))
    ->failure(fn(Throwable $error) => throw $error)
    ->then(fn(CreateAuthorizationModelResponseInterface $model) => $model->getId())
    ->success(fn($id) => print "Authorization Model created! ID: {$id}\n")
    ->unwrap();

/**
 * 4. Write a Relationship Tuple
 */

$tuple = tuple(
    type: 'user',
    relation: 'viewer',
    object: 'document:roadmap',
);

($client->writeTuples(store: $store, model: $model, writes: tuples($tuple)))
    ->success(fn(WriteTuplesResponseInterface $response) => print "Anne is now allowed to view the roadmap document\n")
    ->failure(fn(Throwable $error) => throw $error)
    ->unwrap();

/**
 * 5. Perform an Authorization Check
 */

$allowed = ($client->check(store: $store, model: $model, tupleKey: $tuple))
    ->then(fn(CheckResponseInterface $response) => $response->getIsAllowed())
    ->unwrap();

match ($allowed) {
    true => print "SUCCESS: Anne CAN view the roadmap!\n",
    false => print "FAILURE: Anne CANNOT view the roadmap.\n",
};

// For a more detailed walkthrough, see docs/GettingStarted.md
?>
```

For robust error handling, explore the `Success`/`Failure` patterns described in [Error Handling and Results](docs/Results.md).

## Core Concepts

The SDK provides a fluent interface for interacting with all the core features of OpenFGA. Here's a brief overview:

- **[Stores](docs/Stores.md):** Isolated environments for your authorization models and relationship data. They allow you to manage permissions separately for different applications or tenants.

- **[Authorization Models](docs/AuthorizationModels.md):** These define the structure of your access control system – the types of objects, the possible relationships (e.g., `viewer`, `editor`), and how those relationships grant permissions. You can define models using a human-readable DSL.

- **[Relationship Tuples](docs/RelationshipTuples.md):** These are what define **who** has **what** relationship with **which** object (e.g., "user:anne is a `viewer` of document:roadmap"). You can write, read, and delete these tuples.

- **[Queries](docs/Queries.md):** Ask powerful questions about your authorization data:

  - `check()`: "Does this user have this permission on this object?"
  - `expand()`: "How does this user have this permission?"
  - `listUsers()`: "Which users have this permission on this object?"
  - `listObjects()`: "Which objects of a certain type can this user access with this permission?"

- **[Assertions](docs/Assertions.md):** These are used for testing your authorization models. You can assert whether specific relationships should exist or not, helping you verify your model's correctness.

## Authentication

The SDK can be configured to use any of the authentication methods supported by OpenFGA and Auth0 FGA:

- No authentication (default)
- Pre-shared key authentication (token)
- OIDC (client credentials flow)

[Learn more about configuring authentication in `docs/Authentication.md`](docs/Authentication.md)

## Error Handling and Results

Client methods return `Result` objects representing either a `Success` or a `Failure` outcome. This pattern allows for explicit error handling. Helper functions like `unwrap()`, `fold()`, `success()`, and `failure()` are provided to simplify working with these results.

[Learn more about handling results and errors in `docs/Results.md`](docs/Results.md)

## Documentation

For more detailed information, please refer to the following resources:

- **SDK Documentation** can be found in the `docs` directory.
  - [Getting Started](docs/GettingStarted.md)
  - [Authentication](docs/Authentication.md)
  - [Stores](docs/Stores.md)
  - [Authorization Models](docs/AuthorizationModels.md)
  - [Relationship Tuples](docs/RelationshipTuples.md)
  - [Queries](docs/Queries.md)
  - [Assertions](docs/Assertions.md)
  - [Results (Error Handling)](docs/Results.md)
  - [Generated API Documentation](docs/API)
- **SDK Examples** can be found in the `examples` directory.
- **OpenFGA** provides [comprehensive server documentation](https://openfga.dev/docs).

## Contributing

We greatly appreciate all contributions to the OpenFGA PHP SDK. Please see the [CONTRIBUTING.md](.github/CONTRIBUTING.md) file for more information on how to contribute.

## Code of Conduct

In order to ensure that the community is welcoming to all, please review and abide by the [Code of Conduct](CODE_OF_CONDUCT.md).

## Security Vulnerabilities

If you discover a security vulnerability within the OpenFGA PHP SDK, please submit a [vulnerability report](https://github.com/evansims/openfga-php/security). All security vulnerabilities will be promptly addressed.

## License

The OpenFGA PHP SDK is open-sourced software licensed under [Apache 2.0](LICENSE).

---

This project is an unofficial, community-maintained SDK and is not endorsed by OpenFGA or Auth0.
