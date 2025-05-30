<p align="center">
  <a href="https://tempestphp.com">
    <img src=".github/openfga.png" width="100" />
  </a>
</p>

<h1 align="center">OpenFGA PHP SDK</h1>

<p align="center">
  <a href="https://codecov.io/gh/evansims/openfga-php" target="_blank"><img src="https://codecov.io/gh/evansims/openfga-php/graph/badge.svg" alt="codecov" /></a>
  <a href="https://shepherd.dev/github/evansims/openfga-php" target="_blank"><img src="https://shepherd.dev/github/evansims/openfga-php/coverage.svg" alt="Psalm Type Coverage" /></a>
  <a href="https://www.bestpractices.dev/projects/10666"><img src="https://www.bestpractices.dev/projects/10666/badge"></a>
</p>

<p align="center">
  Stop writing authorization logic. Start asking questions.<br />
  <a href="/docs/README.md">Read the documentation</a> to get started.
</p>

<br />

## Why OpenFGA?

Every app needs permissions. Most developers end up with authorization logic scattered across controllers, middleware, and business logic. Changes break things. New features require touching dozens of files.

**[OpenFGA](https://openfga.dev/) solves this.** Define your authorization rules once, query them anywhere. This SDK provides a modern PHP interface to [OpenFGA](https://openfga.dev/) and [Auth0 FGA](https://auth0.com/fga).

## Quick start

```bash
composer require evansims/openfga-php
```

```php
use OpenFGA\Client;
use function OpenFGA\{allowed, tuple};

$client = new Client(url: 'http://localhost:8080');

// Instead of scattered if statements in your controllers:
if ($user->isAdmin() || $user->owns($document) || $user->team->canEdit($document)) {
    // ...
}

// Ask OpenFGA:
$canEdit = allowed(
    client: $client,
    store: 'my-store',
    model: 'my-model',
    tuple: tuple('user:alice', 'editor', 'document:readme')
);

// Zero business logic coupling. Pure authorization.
```

## Highlights

- **Zero business logic coupling** — Authorization stays separate from your domain code
- **Scalable architecture** — Battle-tested relationship-based access control patterns inspired by Google Zanzibar
- **Type-safe by design** — Complete type hints, strict typing, and full IDE support
- **Human-readable DSL** — Define authorization models with intuitive syntax
- **Production ready** — OpenTelemetry observability, retry logic, and circuit breakers included
- **Developer experience first** — Stewarded by 30+ years of PHP expertise with intuitive APIs and comprehensive documentation

## Learn more

Ready to build bulletproof authorization? See [the documentation](docs/README.md) for:

- **[Getting started](docs/Introduction.md)** — Build your first authorization system in 10 minutes
- **[Authorization models](docs/Models.md)** — Define permission rules with intuitive DSL
- **[Queries](docs/Queries.md)** — Check permissions and list accessible resources
- **[Authentication](docs/Authentication.md)** — Secure your production setup

## Installation

```bash
composer require evansims/openfga-php
```

See [the documentation](docs/README.md) for configuration and setup.

## Contributing

Contributions are welcome — have a look at our [contributing guidelines](.github/CONTRIBUTING.md).
