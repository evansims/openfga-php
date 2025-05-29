# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg)](https://codecov.io/gh/evansims/openfga-php)
[![Psalm Type Coverage](https://shepherd.dev/github/evansims/openfga-php/coverage.svg)](https://shepherd.dev/github/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

**The OpenFGA PHP SDK puts relationship-based access control at your fingertips.** Describe “who can do what” once in a human-friendly DSL, then let [OpenFGA](https://openfga.dev/) or [Auth0 FGA](https://auth0.com/fga) enforce it. No scattering of `if` statements across your codebase or brittle policy files. A fluent API with strict typing, and PSR-compatible networking makes all the plumbing disappear, unburdening you with cleaner code, sharper security boundaries, and room to grow.

**Quick Navigation:** [⚡ Installation](#installation) • [🚀 Quick Start](#quick-start) • [📚 Full Documentation](docs/GettingStarted.md) • [🔧 API Reference](docs/API)

## Highlights

- **Strictly-typed API** — Native type hinting and linter-friendly generics catch errors before they reach production
- **Standards first** — Built for interoperability with any PSR-compatible HTTP library you prefer
- **Human-friendly DSL** — Parse and commit authorization model changes using plain text instead of complex JSON
- **Ready to scale** — One solution from weekend projects to enterprise apps with millions of tuples
- **Community-maintained** — Stewarded by seasoned PHP contributors; dedicated to stability and security
- **Battle-tested** — Comprehensive test coverage and used in production by teams worldwide

## Requirements

- PHP 8.3+
- Composer
- [OpenFGA](https://openfga.dev/) or [Auth0 FGA](https://auth0.com/fga)
- PSR-7, PSR-17, and PSR-18 networking libraries

## Installation

Install the SDK via Composer:

```bash
composer require evansims/openfga-php
```

You'll also need PSR-7, PSR-17, and PSR-18 implementations. Any PSR-compatible networking libraries will work. The SDK adapts to whatever you're already using, so choose what works best for your project.

## Quick Start

Ready to see it in action? This 5-minute example shows you the complete flow: setup → define permissions → check access.

**What you'll build:** A simple document system where users can be viewers or editors.

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use OpenFGA\Client;
use OpenFGA\Models\AuthorizationModel;
use OpenFGA\Responses\{
    CreateStoreResponseInterface,
    CreateAuthorizationModelResponseInterface,
    CheckResponseInterface
};

use function OpenFGA\Models\{tuple, tuples};

define('STORE_NAME', 'my-php-store');

// 1. Initialize the SDK Client

$client = new Client(
    url: 'http://localhost:8080',
);

// 2. Create a Store

$store = ($client->createStore(name: STORE_NAME))
    ->then(fn(CreateStoreResponseInterface $store) => $store->getId())
    ->success(fn($id) => print "Store created! ID: {$id}\n")
    ->unwrap();

// 3. Create an Authorization Model from a DSL

$dsl = <<<DSL
    model
        schema 1.1

    type user

    type document
        relations
        define viewer: [user]
DSL;

$model = ($client->dsl($dsl))
    ->then(fn(AuthorizationModel $model) => $client->createAuthorizationModel(
        store: $store,
        typeDefinitions: $model->getTypeDefinitions(),
        conditions: $model->getConditions(),
    ))
    ->then(fn(CreateAuthorizationModelResponseInterface $model) => $model->getModel())
    ->success(fn($id) => print "Authorization Model created! ID: {$id}\n")
    ->unwrap();

// 4. Write a Relationship Tuple

$tuple = tuple(
    user: 'user:anne',
    relation: 'viewer',
    object: 'document:roadmap',
);

$client->writeTuples(store: $store, model: $model, writes: tuples($tuple))
    ->success(fn() => print "Anne can now view the roadmap document\n");

// 5. Perform an Authorization Check

$allowed = $client->check(store: $store, model: $model, tupleKey: $tuple)
    ->unwrap(fn(CheckResponseInterface $response) => $response->getAllowed());

match ($allowed) {
    true => print "SUCCESS: Anne CAN view the roadmap!\n",
    false => print "FAILURE: Anne CANNOT view the roadmap.\n",
};

// For a more detailed walkthrough, see docs/GettingStarted.md
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

Secure your connection to OpenFGA with any supported authentication method:

- **🔓 No authentication** — Perfect for local development and testing
- **🔑 API Token** — Simple pre-shared key authentication for internal services
- **🛡️ OAuth2 Client Credentials** — Enterprise-grade security for production (Auth0 FGA, etc.)

**Quick Setup:**

```php
// For production with Auth0 FGA
$client = new Client(
    url: 'https://api.us1.fga.dev',
    authentication: Authentication::CLIENT_CREDENTIALS,
    clientId: $_ENV['FGA_CLIENT_ID'],
    clientSecret: $_ENV['FGA_CLIENT_SECRET'],
    issuer: $_ENV['FGA_ISSUER'],
    audience: $_ENV['FGA_AUDIENCE']
);
```

[Complete authentication guide →](docs/Authentication.md)

## Error Handling and Results

Client methods return `Result` objects representing either a `Success` or a `Failure` outcome. This pattern allows for explicit error handling. Helper functions like `unwrap()`, `success()`, and `failure()` are provided to simplify working with these results.

[Learn more about handling results and errors in `docs/Results.md`](docs/Results.md)

## Documentation

### 📖 Getting Started

- **[Getting Started Guide](docs/GettingStarted.md)** — Complete walkthrough from installation to first auth check
- **[Core Concepts](docs/AuthorizationModels.md)** — Understanding models, stores, and relationships
- **[Common Patterns](examples/)** — Real-world examples and use cases

### 🔧 Implementation Guides

- **[Authentication Setup](docs/Authentication.md)** — Connect securely to OpenFGA/Auth0 FGA
- **[Managing Stores](docs/Stores.md)** — Multi-tenant and environment isolation
- **[Writing Queries](docs/Queries.md)** — Check permissions and list resources
- **[Error Handling](docs/Results.md)** — Robust production patterns

### 🔍 Advanced Topics

- **[Relationship Tuples](docs/RelationshipTuples.md)** — Managing permissions at scale
- **[Testing Models](docs/Assertions.md)** — Validate your authorization logic
- **[API Reference](docs/API)** — Complete class and method documentation

### 🌐 External Resources

- **[OpenFGA Documentation](https://openfga.dev/docs)** — Server setup and concepts
- **[Auth0 FGA](https://auth0.com/docs/fga)** — Managed service documentation

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
