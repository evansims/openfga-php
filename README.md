# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

High-performance fine-grained authorization for PHP applications with [OpenFGA](https://openfga.dev/)/[Auth0 FGA](https://auth0.com/fine-grained-authorization).

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

Please ensure your project has the [required dependencies](#requirements) installed.

## Quick Start

Create a client. The only required parameter is the URL of your OpenFGA instance.

```php
use OpenFGA\{Client, Authentication};

use function OpenFGA\Results\{fold, success, failure, unwrap};

$client = new Client(
    url: $_ENV['FGA_API_URL'],
);
```

- **Authentication:** None by default, but pre-shared keys and client credentials [are supported](docs/Authentication.md).
- **Result Functions:** Helpers for working with [Results](docs/Results.md) returned by client methods.

## Stores

Stores are isolated environments that contain their own authorization model and data, allowing you to manage permissions separately for different applications, tenants, or environments.

**Create a store** with `Client::createStore()`:

```php
use OpenFGA\Responses\CreateStoreResponseInterface;

$result = $client->createStore(name: 'my-store');

echo fold(
    $result,
    fn(CreateStoreResponseInterface $response) => "Store created: {$response->getId()}",
    fn(Throwable $err) => "Store creation failed: {$err->getMessage()}"
);

failure($result, fn(Throwable $error) => {
    // Store creation failed. Log or gracefully handle the error.
    throw $error;
});
```

**List stores** with `Client::listStores()`:

```php
use OpenFGA\Responses\ListStoresResponseInterface;

$result = $client->listStores();

success($result, fn(ListStoresResponseInterface $response) => {
    foreach ($response->getStores() as $store) {
        echo "Store: {$store->getId()}\n";
    }
});
```

**Get a store** with `Client::getStore()`:

```php
use OpenFGA\Responses\GetStoreResponseInterface;

($client->getStore(store: 'my-store'))->onSuccess(fn(GetStoreResponseInterface $response) => {
    echo "Store ID: {$response->getId()}\n";
    echo "Store Name: {$response->getName()}\n";
    echo "Store Created At: {$response->getCreatedAt()->format('Y-m-d H:i:s')}\n";
    echo "Store Updated At: {$response->getUpdatedAt()->format('Y-m-d H:i:s')}\n";
});
```

**Delete a store** with `Client::deleteStore()`:

```php
success($store, fn(CreateStoreResponseInterface $store) => {
    // Delete the store we created with our `createStore()` call above.
    $client->deleteStore(store: $store->getId());
});
```

See [docs/Stores.md](docs/Stores.md) for more information.

## Authorization Models

Authorization models define the structure of your access control system: what types of objects exist, what relationships (like viewer or owner) they support, and how access is granted. They’re the blueprint that tells the system how to interpret and enforce permissions.

**Transform a [DSL](https://openfga.dev/docs/configuration-language)** into an `AuthorizationModel` with `Client::dsl()`:

```php
use OpenFGA\Models\AuthorizationModel;

$dsl = <<<DSL
model
schema 1.1

type user

type domain
relations
    define member: [user]

type folder
relations
    define can_share: writer
    define owner: [user, domain#member] or owner from parent_folder
    define parent_folder: [folder]
    define viewer: [user, domain#member] or writer or viewer from parent_folder
    define writer: [user, domain#member] or owner or writer from parent_folder

type document
relations
    define can_share: writer
    define owner: [user, domain#member] or owner from parent_folder
    define parent_folder: [folder]
    define viewer: [user, domain#member] or writer or viewer from parent_folder
    define writer: [user, domain#member] or owner or writer from parent_folder
DSL;

$result = $client->dsl($dsl);

echo fold(
    $result,
    fn(AuthorizationModelInterface $model) => "DSL transformed successfully!",
    fn(Throwable $err) => "DSL transformation failed: {$err->getMessage()}"
);

failure($result, fn (Throwable $error): never => {
    // Handle transformation failure
    throw $error;
});

$model = unwrap($result);
```

**Create an authorization model** with `Client::createAuthorizationModel()`:

```php
use OpenFGA\Responses\CreateAuthorizationModelResponseInterface;

($client->createAuthorizationModel(
    store: $store->getId(),
    typeDefinitions: $model->getTypeDefinitions(),
    conditions: $model->getConditions(),
))->onSuccess(fn(CreateAuthorizationModelResponseInterface $response) => {
    echo "Created Authorization Model ID: {$response->getModel()}\n";
});
```

**List authorization models** with `Client::listAuthorizationModels()`:

```php
use OpenFGA\Responses\ListAuthorizationModelsResponseInterface;

$continuationToken = null;

for ($i = 0; $i < 10; $i++) {
    $result = $client->listAuthorizationModels(
        store: $store->getId(),
        continuationToken: $continuationToken,
    );

    success($result, fn (ListAuthorizationModelsResponseInterface $response) => {
        foreach ($response->getModels() as $model) {
            echo "Authorization Model ID: {$model->getId()}\n";
        }

        $continuationToken = $response->getContinuationToken();
    });

    if (failure($result) || $continuationToken === null) {
        break;
    }
}
```

- The `continuationToken` parameter is only necessary when paginating results.
- The `pageSize` parameter controls the number of models returned per pagination request.

**Get an authorization model** with `Client::getAuthorizationModel()`:

```php
use OpenFGA\Responses\GetAuthorizationModelResponseInterface;

$result = $client->getAuthorizationModel(
    store: $store->getId(),
    model: $model->getId()
);

success($result, fn(GetAuthorizationModelResponseInterface $response) => {
    $model = $response->getModel();

    echo "Authorization Model ID: {$model->getId()}\n\n";

    // Transform the AuthorizationModel into a DSL string with `AuthorizationModel::dsl()`.
    echo "Authorization Model DSL:\n\n{$model->dsl()}\n";
});
```

See [docs/AuthorizationModels.md](docs/AuthorizationModels.md) for more information.

## Relationship Tuples

**Write tuples** with `Client::writeTuples()`:

Add or remove relationship tuples, essentially updating who has access to what.

```php
use OpenFGA\Responses\WriteTuplesResponseInterface;
use OpenFGA\Models\{TupleKey, TupleKeys};

$tuples = new TupleKeys([
    new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:roadmap'
    ),
]);

$result = $client->writeTuples(
    store: $store->getId(),
    model: $model->getId(),
    writes: $tuples,
);

echo fold(
    $result,
    fn(WriteTuplesResponseInterface $response) => "Tuples written successfully.",
    fn(Throwable $err) => "Tuples write failed: {$err->getMessage()}"
);
```

- The `writes` parameter controls the tuples to write.
- The `deletes` parameter controls the tuples to delete.

**Read tuples** with `Client::readTuples()`:

Returns all the relationship tuples (permissions data) that match a given filter, such as all users who are viewers of a document.

```php
use OpenFGA\Responses\ReadTuplesResponseInterface;
use OpenFGA\Models\TupleKey;

$tuple = new TupleKey(
    user: 'user:anne',
    relation: 'reader',
    object: 'document:roadmap'
);

$continuationToken = null;

for ($i = 0; $i < 10; $i++) {
    $result = $client->readTuples(
        store: $store->getId(),
        tupleKey: $tuple,
        continuationToken: $continuationToken,
    );

    success($result, fn(ReadTuplesResponseInterface $response) => {
        foreach ($response->getTuples() as $tuple) {
            echo "Tuple: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
        }

        $continuationToken = $response->getContinuationToken();
    });

    if (failure($result) || $continuationToken === null) {
        break;
    }
}
```

- The `continuationToken` parameter is only necessary when paginating results.
- The `pageSize` parameter controls the number of tuples returned per pagination request.
- The `consistency` parameter controls the consistency of the read operation.

**List tuple changes** with `Client::listTupleChanges()`:

Returns a stream of recent authorization data changes (like added or removed relationships) so you can track updates over time.

```php
use OpenFGA\Responses\ListTupleChangesResponseInterface;

$continuationToken = null;

for ($i = 0; $i < 10; $i++) {
    $result = $client->listTupleChanges(
        store: $store->getId(),
        continuationToken: $continuationToken,
    );

    success($result, fn(ListTupleChangesResponseInterface $response) => {
        foreach ($response->getChanges() as $change) {
            echo "Change: {$change->getUser()} {$change->getRelation()} {$change->getObject()}\n";
        }

        $continuationToken = $response->getContinuationToken();
    });

    if (failure($result) || $continuationToken === null) {
        break;
    }
}
```

- The `continuationToken` parameter is only necessary when paginating results.
- The `pageSize` parameter controls the number of changes returned per pagination request.
- The `type` parameter controls the type of changes returned.
- The `startTime` parameter controls the start time of the changes returned.

See [docs/RelationshipTuples.md](docs/RelationshipTuples.md) for more information.

## Relationship Queries

**Check** with `Client::check()`:

Returns a simple answer to the question: "_Does_ this user have this access to this object?"

```php
use OpenFGA\Responses\CheckResponseInterface;
use OpenFGA\Models\TupleKey;

$tuple = new TupleKey(
    user: 'user:anne',
    relation: 'reader',
    object: 'document:roadmap',
)

$result = ($client->check(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: $tuple,
))->then(fn(CheckResponseInterface $response) => $response->getIsAllowed());

$allowed = $result->unwrap(false);
```

**Expand** with `Client::expand()`:

Returns a detailed tree of all the possible access paths granting a specific relation on an object. "_How_ does this user have access to this object?"

```php
use OpenFGA\Responses\ExpandResponseInterface;

$tuple = new TupleKey(
    relation: 'reader',
    object: 'document:roadmap',
)

$result = $client->expand(
    store: $store->getId(),
    tupleKey: $tuple,
);

success($result, fn(ExpandResponseInterface $response) => {
    foreach ($response->getTuples() as $tuple) {
        echo "Tuple: {$tuple->getUser()} {$tuple->getRelation()} {$tuple->getObject()}\n";
    }
});
```

**List users** with `Client::listUsers()`:

Returns all users who have a specific type of access to a given object. "_Who_ has access to this object?"

```php
use OpenFGA\Responses\ListUsersResponseInterface;

$result = $client->listUsers(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: new TupleKey(relation: 'viewer', object: 'document:roadmap'),
);

success($result, fn(ListUsersResponseInterface $response) => {
    foreach ($response->getUsers() as $user) {
        echo "User: {$user->getUser()} {$user->getRelation()} {$user->getObject()}\n";
    }
});
```

**List objects** with `Client::listObjects()`:

Returns all the objects a specific user has a certain type of access to (like all documents they can view). "_What_ objects does this user have access to?"

```php
use OpenFGA\Responses\ListObjectsResponseInterface;

$result = $client->listObjects(
    store: $store->getId(),
    model: $model->getId(),
    tupleKey: new TupleKey(relation: 'viewer', object: 'document:roadmap'),
);

success($result, fn(ListObjectsResponseInterface $response) => {
    foreach ($response->getObjects() as $object) {
        echo "Object: {$object->getObject()} {$object->getRelation()} {$object->getUser()}\n";
    }
});
```

See [docs/Queries.md](docs/Queries.md) for more information.

## Assertions

Assertions are test cases that check whether specific users should or shouldn’t have access to certain resources, based on your authorization model. They help you validate that your access rules behave as expected and catch mistakes early.

**Read assertions** with `Client::readAssertions()`:

```php
use OpenFGA\Responses\ReadAssertionsResponseInterface;

$result = $client->readAssertions(
    store: $store->getId(),
    model: $model->getId(),
);

success($result, fn(ReadAssertionsResponseInterface $response) => {
    foreach ($response->getAssertions() as $assertion) {
        echo "Assertion: {$assertion->getAssertion()} {$assertion->getRelation()} {$assertion->getObject()}\n";
    }
});
```

**Write assertions** with `Client::writeAssertions()`:

```php
use OpenFGA\Responses\WriteAssertionsResponseInterface;
use OpenFGA\Models\{Assertion, TupleKey};
use OpenFGA\Collections\Assertions;

$assertions = new Assertions([
    new Assertion(
        tupleKey: new TupleKey(
            user: 'user:anne',
            relation: 'reader',
            object: 'document:roadmap',
        ),
        expectation: true,
    ),
]);

$result = $client->writeAssertions(
    store: $store->getId(),
    model: $model->getId(),
    assertions: $assertions,
);

success($result, fn(WriteAssertionsResponseInterface $response) => {
    foreach ($response->getAssertions() as $assertion) {
        echo "Assertion: {$assertion->getAssertion()} {$assertion->getRelation()} {$assertion->getObject()}\n";
    }
});
```

See [docs/Assertions.md](docs/Assertions.md) for more information.

## Documentation

- [SDK API Technical Reference](docs/API)
- [OpenFGA Documentation](https://openfga.dev/docs)

---

This project is an unofficial, community-maintained SDK and is not endorsed by OpenFGA or Auth0.

Licensed under [the Apache 2.0 License](LICENSE).
