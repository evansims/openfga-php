<p align="center">
  <a href="https://tempestphp.com">
    <img src=".github/openfga.png" width="100" />
  </a>
</p>

<h1 align="center">OpenFGA PHP SDK</h1>

<p align="center">
  <a href="https://codecov.io/gh/evansims/openfga-php" target="_blank"><img src="https://codecov.io/gh/evansims/openfga-php/graph/badge.svg" alt="codecov" /></a>
  <a href="https://shepherd.dev/github/evansims/openfga-php" target="_blank"><img src="https://shepherd.dev/github/evansims/openfga-php/coverage.svg" alt="Psalm Type Coverage" /></a>
</p>

<p align="center">
  A PHP SDK for <a href="https://openfga.dev/">OpenFGA</a> and <a href="https://auth0.com/fine-grained-authorization">Auth0 FGA</a>.<br />
  Read the <a href="/docs/README.md">documentation</a> to get started.
</p>

<br />

## Introduction

[OpenFGA](https://openfga.dev/) is a relationship-based access control (ReBAC) system that makes authorization simple and scalable. This SDK makes integrating OpenFGA into your PHP application straightforward.

- Configure the SDK ↘

  ```php
  use OpenFGA\Client;

  $client = new Client(
    url: 'http://localhost:8080'
  );
  ```

- Create [a store](docs/Stores.md) ↘

  ```php
  use function OpenFGA\Models\store;

  $store = store(
    client: $client,
    name: 'my-php-store',
  );
  ```

- Create [an authorization model](docs/AuthorizationModels.md) ↘

  ```php
  use function OpenFGA\Models\{dsl, model};

  $dsl = dsl(<<<'DSL'
    model
    schema 1.1

    type user

    type document
      relations
        define viewer: [user]
        define editor: [user] and viewer
  DSL);

  $model = model(
    client: $client,
    store: $store,
    typeDefinitions: $dsl->getTypeDefinitions(),
  );
  ```

- Create [a relationship tuple](docs/Tuples.md) ↘

  ```php
  use function OpenFGA\Models\tuple;
  use function OpenFGA\Requests\write;

  $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

  // "Anne has viewer access to roadmap"
  write(
    client: $client,
    store: $store,
    model: $model,
    tuples: $tuple,
  );
  ```

- [Query](docs/Queries.md) for a relationship ↘

  ```php
  use function OpenFGA\Models\tuple;
  use function OpenFGA\Requests\allowed;

  $tuple = tuple('user:anne', 'viewer', 'document:roadmap');

  // "Can Anne view the roadmap?"
  allowed(
    client: $client,
    store: $store,
    model: $model,
    tuples: $tuple,
  );
  ```

See [the documentation](docs/README.md) for more help on getting started.

## Installation

```bash
composer require evansims/openfga-php
```

See [the documentation](docs/README.md) for more information.

## Contributing

Contributions are welcome — have a look at our [contributing guidelines](.github/CONTRIBUTING.md).
