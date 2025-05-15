<?php

/*
1. DataClass representing the "raw" response from the API
2. OpClass wraps the DataClass and provides a more PHP-idiomatic interface for performing operations
*/

use OpenFga\{Client, Configuration};

$fga = new Client(
    configuration: new Configuration(
        url: 'https://openfga.example.com',
    ),
);

// -----

// list<Store>
$stores = $fga->listStores();

// Store
$store = $fga->createStore(
    name: 'my-store-name'
);

// Store
$store = $fga->getStore(
    store: $store->getId()
);

$fga->deleteStore(
    store: $store
);

// -----

// list<AuthorizationModel>
$models = $fga->listAuthorizationModels();

// AuthorizationModel
$model = $fga->createAuthorizationModel(
    store: $store,
    name: 'my-model-name',
    schema: [
        [
            'type' => 'user',
            'id_attribute' => 'id',
        ],
        [
            'type' => 'object',
            'id_attribute' => 'id',
        ],
    ],
);

// AuthorizationModel
$model = $fga->getAuthorizationModel(
    store: $store,
    model: $model
);

// -----

// list<Assertion>
$assertions = $fga->readAssertions(
    store: $store, // string | Store
    model: $model // string | AuthorizationModel
);

$fga->writeAssertions(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    assertions: [
        [
            'relation' => 'admin',
            'users' => ['user:1'],
            'objects' => ['user:2'],
        ],
    ]
);

// -----

// list<Change>
$changes = $fga->listTupleChanges(
    store: $store // string | Store
);

// list<Tuple>
$tuples = $fga->readTuples(
    store: $store, // string | Store
    model: $model // string | AuthorizationModel
);

$fga->writeTuples(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    tuples: [
        [
            'relation' => 'admin',
            'users' => ['user:1'],
            'objects' => ['user:2'],
        ],
    ]
);

// -----

// CheckResponse
$allowed = $fga->check(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    check: [
        [
            'relation' => 'admin',
            'user' => 'user:1',
            'object' => 'user:2',
        ],
    ]
);

// ExpandResponse
$expanded = $fga->expand(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    expand: [
        [
            'relation' => 'admin',
            'user' => 'user:1',
            'object' => 'user:2',
        ],
    ]
);

// list<Object>
$objects = $fga->listObjects(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    relation: 'admin',
    user: 'user:1',
);

// list<User>
$users = $fga->listUsers(
    store: $store, // string | Store
    model: $model, // string | AuthorizationModel
    relation: 'admin',
    object: 'user:1',
);
