# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

A high-performance PHP client for [OpenFGA](https://openfga.dev/) and [Auth0 FGA](https://auth0.com/fine-grained-authorization).

## Requirements

- PHP 8.3+
- Composer
- OpenFGA or Auth0 FGA
- Dependencies:
  - Any [PSR-18 implementation](https://packagist.org/providers/psr/http-client-implementation)
  - Any [PSR-17 implementation](https://packagist.org/providers/psr/http-factory-implementation)
  - Any [PSR-7 implementation](https://packagist.org/providers/psr/http-message-implementation)

## Installation

```bash
composer require evansims/openfga-php
```

## Quick start

Create a client using a shared key or client credentials. Only the URL is required when no authentication is needed.

```php
use OpenFGA\{Client, Authentication};

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authentication: Authentication::TOKEN,
    token: $_ENV['FGA_SHARED_KEY'] ?? null,
);
```

```php
$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authentication: Authentication::CLIENT_CREDENTIALS,
    clientId: $_ENV['FGA_CLIENT_ID'],
    clientSecret: $_ENV['FGA_CLIENT_SECRET'],
    issuer: $_ENV['FGA_API_TOKEN_ISSUER'],
    audience: $_ENV['FGA_API_AUDIENCE'],
);
```

## Working with stores

```php
$response = $client->createStore('my-store');
$stores   = $client->listStores();
$store    = $client->getStore($response->getId());
$client->deleteStore($store->getId());
```

See [docs/Stores.md](docs/Stores.md) for more information.

## Authorization models

```php
$models = $client->listAuthorizationModels($store->getId());
$model  = $client->createAuthorizationModel(
    store: $store->getId(),
    typeDefinitions: $types,
    conditions: $conditions,
);
$detail = $client->getAuthorizationModel($store->getId(), $model->getId());
```

See [docs/AuthorizationModels.md](docs/AuthorizationModels.md) for more information.

## Relationship tuples

```php
$client->writeTuples(
    store: $store->getId(),
    model: $model->getId(),
    writes: $tuples,
);

$read = $client->readTuples($store->getId(), new TupleKey(...));
$changes = $client->listTupleChanges($store->getId());
```

See [docs/RelationshipTuples.md](docs/RelationshipTuples.md) for more information.

## Relationship queries

```php
$check = $client->check(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: new TupleKey(user: 'user:anne', relation: 'reader', object: 'document:roadmap')
);

$tree = $client->expand($store->getId(), new TupleKey(relation: 'viewer', object: 'document:roadmap'));

$users = $client->listUsers($store->getId(), $model->getId(), 'document:roadmap', 'viewer', $filters);

$objects = $client->listObjects($store->getId(), $model->getId(), 'document', 'viewer', 'user:anne');
```

See [docs/Queries.md](docs/Queries.md) for more information.

### Streaming objects

```php
$stream = $client->streamedListObjects(
    store: $store->getId(),
    model: $model->getId(),
    type: 'document',
    relation: 'viewer',
    user: 'user:anne'
);
```

## Assertions

```php
$assertions = $client->readAssertions($store->getId(), $model->getId());
$client->writeAssertions($store->getId(), $model->getId(), $assertions);
```

See [docs/Assertions.md](docs/Assertions.md) for more information.

---

This project is an unofficial, community-maintained SDK. It is not endorsed by OpenFGA or Auth0. Licensed under [the Apache 2.0 License](LICENSE).
