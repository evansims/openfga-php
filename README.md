# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg?token=DYXS91T0S)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

A high performance PHP client for [OpenFGA](https://openfga.dev/) and [Auth0 FGA](https://auth0.com/fine-grained-authorization).

## Requirements

- PHP 8.3+
- Composer
- A PSR-18 HTTP client, PSR-17 factories and PSR-7 implementation
- A running OpenFGA or Auth0 FGA server

## Installation

```bash
composer require evansims/openfga-php
```

If your project does not already provide PSR implementations, install any of the popular libraries for your framework.

## Quick start

Create a client using a shared key or client credentials. Only the URL is required when no authentication is needed.

```php
use OpenFGA\Authentication\AuthenticationMode;
use OpenFGA\Client;

$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authenticationMode: AuthenticationMode::TOKEN,
    token: $_ENV['FGA_SHARED_KEY'] ?? null,
);
```

```php
$client = new Client(
    url: $_ENV['FGA_API_URL'],
    authenticationMode: AuthenticationMode::CLIENT_CREDENTIALS,
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

## Relationship tuples

```php
$client->writeTuples(
    store: $store->getId(),
    authorizationModel: $model->getId(),
    writes: $tuples,
);

$read = $client->readTuples($store->getId(), new TupleKey(...));
$changes = $client->listTupleChanges($store->getId());
```

## Relationship queries

```php
$check = $client->check(
    store: $store->getId(),
    authorizationModel: $model->getId(),
    tupleKey: new TupleKey(user: 'user:anne', relation: 'reader', object: 'document:roadmap')
);

$tree = $client->expand($store->getId(), new TupleKey(relation: 'viewer', object: 'document:roadmap'));
$users = $client->listUsers($store->getId(), $model->getId(), 'document:roadmap', 'viewer', $filters);
$objects = $client->listObjects($store->getId(), $model->getId(), 'document', 'viewer', 'user:anne');
```

### Streaming objects

```php
$stream = $client->streamedListObjects(
    store: $store->getId(),
    authorizationModel: $model->getId(),
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

---

Licensed under the Apache 2.0 License.
