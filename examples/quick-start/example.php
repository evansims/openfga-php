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

// 6. Delete the temporary store.

$client->deleteStore(store: $store)
    ->success(fn() => print "Store deleted!\n");
