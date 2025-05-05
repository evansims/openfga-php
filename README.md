# OpenFGA PHP SDK

[![codecov](https://codecov.io/gh/evansims/openfga-php/graph/badge.svg?token=DYXXS91T0S)](https://codecov.io/gh/evansims/openfga-php)
[![OpenSSF Scorecard](https://api.scorecard.dev/projects/github.com/evansims/openfga-php/badge)](https://scorecard.dev/viewer/?uri=github.com/evansims/openfga-php)

An unofficial PHP SDK for [OpenFGA](https://openfga.dev/) and [Auth0 FGA](https://auth0.com/fine-grained-authorization) enabling high performance authorization for modern PHP applications.

- [Requirements](#requirements)
- [Installation](#installation)
  - [PSR Implementations](#psr-implementations)
- [Getting Started](#getting-started)
  - [Credential Configuration](#credential-configuration)
  - [Client Configuration](#client-configuration)
  - [Client Initialization](#client-initialization)
- [Making Requests](#making-requests)
  - [Store Management](#store-management)
    - [Creating a Store](#creating-a-store)
    - [Listing Stores](#listing-stores)
    - [Getting a Store](#getting-a-store)
    - [Deleting a Store](#deleting-a-store)
  - [Authorization Model Management](#authorization-model-management)
    - [Listing Authorization Models for a Store](#listing-authorization-models-for-a-store)
    - [Creating a new Authorization Model](#creating-a-new-authorization-model)
    - [Getting an Authorization Model](#getting-an-authorization-model)
  - [Relationship Tuples](#relationship-tuples)
    - [Listing Tuple Changes](#listing-tuple-changes)
    - [Creating a Relationship Tuple](#creating-a-relationship-tuple)
    - [Querying Relationship Tuples](#querying-relationship-tuples)
    - [Reading Relationship Tuple Changes](#reading-relationship-tuple-changes)
  - [Relationship Queries](#relationship-queries)
    - [Checking for Authorization](#checking-for-authorization)
    - [Expanding Relationship Checks](#expanding-relationship-checks)
    - [Listing User-Type Relationships](#listing-user-type-relationships)
    - [Listing User-Object Relationships](#listing-user-object-relationships)
    - [Streaming User-Object Relationships](#streaming-user-object-relationships)
  - [Assertions](#assertions)
    - [Reading Assertions](#reading-assertions)
    - [Writing Assertions](#writing-assertions)

## Requirements

- [PHP 8.3+](https://www.php.net/)
- [Composer](https://getcomposer.org/)
- [PSR Implementations](#psr-implementations)
- [OpenFGA](https://openfga.dev/docs/getting-started/setup-openfga/overview) or [Auth0 FGA](https://auth0.com/fine-grained-authorization)

## Installation

Add the SDK as a dependency to your application using [Composer](https://getcomposer.org/):

```bash
composer require evansims/openfga-php
```

### PSR Implementations

The SDK is built around the [PSR-18](https://www.php-fig.org/psr/psr-18), [PSR-17](https://www.php-fig.org/psr/psr-17), and [PSR-7](https://www.php-fig.org/psr/psr-7) interoperability standards. That is to say, you're free to install any compatible networking libraries you prefer, and the SDK will work with them "out of the box".

Your application most likely already has everything necessary, especially if you're using a framework like Laravel or Symfony. Try [installing the SDK](#installation) and see if it works. No errors? You're good to go!

If you do encounter a dependency error, just review the message and identify which `psr/*` implementation(s) your application is missing. Then, use the Packagist links below to find appropriate libraries to install. After installing the libraries, try [installing the SDK](#installation) again.

- [PSR-18 HTTP Factory](https://packagist.org/providers/psr/http-factory-implementation)
- [PSR-17 HTTP Client](https://packagist.org/providers/psr/http-client-implementation)
- [PSR-7 HTTP Message](https://packagist.org/providers/psr/http-message-implementation)

## Usage

### Getting Started

#### Credential Configuration

> [!NOTE]
> If you're [not using authentication](https://openfga.dev/docs/getting-started/setup-sdk-client#using-no-authentication), you can ignore this step.

The SDK supports two types of credentials: [OIDC](https://openfga.dev/docs/getting-started/setup-sdk-client#using-client-credentials-flow) and [shared key](https://openfga.dev/docs/getting-started/setup-sdk-client#using-shared-key-authentication). OIDC ("client credentials flow") credentials are used to authenticate with an Okta FGA instance, while shared key credentials can be used to authenticate with an OpenFGA instance.

To configure the SDK with your credentials, create an appropriate `Credentials` class instance for your authentication type. Later on, you'll pass this instance to the `ClientConfiguration` constructor as the `credentialConfiguration` parameter.

For OIDC ("Client Credentials flow") credentials:

```php
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;

$credential = new ClientCredentialConfiguration(
    apiIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? null,
    apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? null,
    clientId: $_ENV['FGA_CLIENT_ID'] ?? null,
    clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? null,
);
```

Or, when using a shared key:

```php
use OpenFGA\SDK\Configuration\Credentials\SharedKeyCredentialConfiguration;

$credential = new SharedKeyCredentialConfiguration(
    sharedKey: $_ENV['FGA_SHARED_KEY'] ?? null,
);
```

#### Client Configuration

Next, create a `ClientConfiguration` instance. This will be used to configure the SDK client, including the base URL of the FGA instance you're connecting to, the credentials you've configured, and any other options you'd like to set. For example:

```php
use OpenFGA\SDK\Configuration\ClientConfiguration;

$configuration = new ClientConfiguration(
    apiUrl: $_ENV['FGA_API_URL'] ?? null,
    storeId: $_ENV['FGA_STORE_ID'] ?? null,
    authorizationModelId: $_ENV['FGA_MODEL_ID'] ?? null,
    credentialConfiguration: $credential, // Use the credential instance you previously created
);
```

#### Client Initialization

Finally, create a `OpenFGA\Client` instance using the configuration you've set up:

```php
use OpenFGA\Client;

$client = new Client(
  configuration: $configuration
);
```

All set! You're now ready to start making requests to the OpenFGA API.

---

### Making Requests

#### Store Management

##### Listing Stores

This will return a list of all stores. The method returns a `ListStoresResponse` object.

```php
$stores = $client->stores()->list();
```

##### Creating a Store

This will create a store with the name `my-store-name`. The method returns a `CreateStoreResponse` object.

```php
$response = $client->stores()->create(
  name: 'my-store-name'
);

echo $response->getId();
echo $response->getName();
```

##### Getting a Store

This will return the store with the ID `store-id`. The method returns a `GetStoreResponse` object.

```php
$store = $client->store(storeId: 'store-id')->get();

echo $store->getId();
echo $store->getName();
```

##### Deleting a Store

This will delete the store with the ID `store-id`. The method does not return a response, but will throw an exception if the request fails.

```php
$client->store(storeId: 'store-id')->delete();
```

#### Authorization Model Management

##### Listing Authorization Models for a Store

This will return a list of all authorization models for the store with the ID `store-id`.

```php
$store = $client->store(storeId: 'store-id');
$models = $store->models()->list();
```

##### Creating a new Authorization Model

This will create a new authorization model for the store with the ID `store-id`:

```php
$store = $client->store(storeId: 'store-id');

$response = $store->models()->create(
  typeDefinitions: ...,
  schemaVersion: ...,
  conditions: ...
);
```

##### Getting an Authorization Model

This will return the authorization model with the ID `model-id` for the store with the ID `store-id`.

```php
$store = $client->store(storeId: 'store-id');
$model = $store->model(modelId: 'model-id')->get();

echo $model->getId();
```

#### Relationship Tuples

##### Listing Tuple Changes

```php
$store = $client->store(storeId: 'store-id');
$tuples = $store->tuples()->changes();

foreach ($tuples as $tuple->getKey()) {
    echo $tuple->getUser();
    echo $tuple->getRelation();
    echo $tuple->getObject();
}
```

##### Creating a Relationship Tuple

```php
$store = $client->store(storeId: 'store-id');

// Prepare a write operation object.
$op = $store->tuples()->write();

// Ex: create a relationship tuple.
$op->write(
  tuple: new Tuple(
    user: 'user:anne',
    relation: 'writer',
    object: 'document:2021-budget'
  )
);

// Ex: create multiple relationship tuples.
$op->writes(
  tuples: [
    new Tuple(
      user: 'user:anne',
      relation: 'writer',
      object: 'document:2021-budget'
    ),
    new Tuple(
      user: 'user:bob',
      relation: 'reader',
      object: 'document:2021-budget'
    )
  ]
);

// Ex: remove a relationship tuple.
$op->delete(
  tuple: new Tuple(
    user: 'user:anne',
    relation: 'writer',
    object: 'document:2021-budget'
  ),
);

// Ex: remove multiple relationship tuples.
$op->deletes([
  tuples: new Tuple(
    user: 'user:bob',
    relation: 'reader',
    object: 'document:2021-budget'
  )
]);

/*
Execute the operation.
This method will not return a response, but will throw an exception if the request fails.
*/
$op->execute();
```

##### Querying Relationship Tuples

The `query()` method allows you to query for tuples that match a query, without following userset rewrite rules.

For example, to query for all `objects` that `user:bob` has a `reader` relationship with for the `document` type definition:

```php
$store = $client->store(storeId: 'store-id');

$tuples = $store->tuples()->query([
  new TupleKey(
    user: 'user:bob',
    relation: 'reader',
    object: 'document:'
  )
]);

foreach ($tuples as $tuple->getKey()) {
    echo $tuple->getUser();
    echo $tuple->getRelation();
    echo $tuple->getObject();
}
```

Or, to query for all `users` that have `reader` relationship with `document:2021-budget`:

```php
$store = $client->store(storeId: 'store-id');

$tuples = $store->tuples()->query([
  new TupleKey(
    object: 'document:2021-budget:',
    relation: 'reader'
  )
]);

foreach ($tuples as $tuple->getKey()) {
    echo $tuple->getUser();
    echo $tuple->getRelation();
    echo $tuple->getObject();
}
```

Or, to query for all `users` that have `reader` relationship with `document:2021-budget`:

```php
$store = $client->store(storeId: 'store-id');

$tuples = $store->tuples()->query([
  new TupleKey(
    object: 'document:2021-budget:'
  )
]);

foreach ($tuples as $tuple->getKey()) {
    echo $tuple->getUser();
    echo $tuple->getRelation();
    echo $tuple->getObject();
}
```

##### Reading Relationship Tuple Changes

Reads the list of relationship tuple changes (writes and deletes) from a store.

```php
$store = $client->store(storeId: 'store-id');

// Provide a type for filtering, required by the API
$options = new ReadChangesOptions(type: 'document');

$response = $store->tuples()->readChanges(options: $options);

// $response->changes contains an array of TupleChange objects
// $response->continuation_token can be used for pagination

// Example usage:
foreach ($response->changes as $change) {
    $tupleKey = $change->tuple_key;
    $operation = $change->operation; // 'write' or 'delete'
    $timestamp = $change->timestamp;
    // Process the change...
}
```

#### Relationship Queries

##### Checking for Authorization

Checks whether a user has a particular relationship with an object.

```php
$store = $client->store(storeId: 'store-id');

$response = $store->query()->check(
  tuple: new TupleKey(
    user: 'user:anne',
    relation: 'reader',
    object: 'document:roadmap',
  ),
  context: [
    new TupleKey(
      user: 'user:anne',
      relation: 'member',
      object: 'time_slot:office_hours',
    )
  ]
);

$allowed = $response->allowed; // true or false
```

> [!NOTE]
> The Check API caches results for a short time to optimize performance. You can request higher consistency (at the expense of increase latency) using the optional optional `consistency` parameter of the `check()` method. This parameter supports a CONSISTENCY enum value.

##### Expanding Relationship Checks

Expands the relationships for a given object and relation, returning a tree structure of the results.

```php
$store = $client->store(storeId: 'store-id');

$response = $store->query()->expand(
  tuple: new TupleKey(
    relation: 'viewer',
    object: 'document:roadmap'
  ),
  context: [
    new TupleKey(
      user: 'user:anne',
      relation: 'member',
      object: 'time_slot:office_hours',
    )
  ]
);

// $response->tree contains the UsersetTree
// Process the tree structure...
```

##### Listing User-Type Relationships

Lists the users that have a specified relationship with an object. This supports pagination.

```php
$store = $client->store(storeId: 'store-id');

$response = $store->query()->listUsers(
    new ListUsersRequest(
        object: new \OpenFGA\API\Model\FgaObject(type: 'document', id: 'roadmap'),
        relation: 'viewer',
        user_filters: [
            new \OpenFGA\API\Model\UserTypeFilter(type: 'user') // Filter for users of type 'user'
        ],
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// $response->users contains an array of User objects
// Example:
foreach ($response->users as $user) {
    // $user->object->type, $user->object->id
    // or if userset: $user->userset->type, $user->userset->id, $user->userset->relation
}
```

##### Listing User-Object Relationships

Lists the objects of a specific type that the user has a particular relation with.

```php
$store = $client->store(storeId: 'store-id');

$response = $store->query()->listObjects(
    new ListObjectsRequest(
        user: 'user:anne',
        relation: 'viewer',
        type: 'document',
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// $response->objects contains an array of object IDs (strings)
// Example:
foreach ($response->objects as $objectId) {
    // Process the object ID ('document:<id>')
}
```

##### Streaming User-Object Relationships

Provides a streaming equivalent of `ListObjects`. Instead of returning all object IDs at once, it returns a stream that yields each object ID.

_Note: The specific implementation details for handling the stream will depend on the HTTP client used._ The SDK currently returns a `StreamedListObjectsResponse` which might require further processing based on your Guzzle setup for streaming responses.

```php
$store = $client->store(storeId: 'store-id');

// Note: Check SDK/HTTP Client documentation for handling streaming responses
$response = $store->query()->streamedListObjects(
    new StreamedListObjectsRequest(
        user: 'user:anne',
        relation: 'viewer',
        type: 'document',
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// Process the streaming response...
```

#### Assertions

Manage assertions for an authorization model. Assertions are used for testing models.

##### Reading Assertions

Retrieves the assertions for a specific authorization model.

```php
$authorizationModelId = '01G5JAVJ41T49E9TT3SKVS7X1J';

$response = $client->assertions()->read($authorizationModelId);

// $response->assertions is an array of Assertion objects
// Example:
foreach ($response->assertions as $assertion) {
    $tupleKey = $assertion->tuple_key;
    $expectation = $assertion->expectation; // true or false
    // Process assertion...
}
```

##### Writing Assertions

Updates the assertions for a specific authorization model. This overwrites any existing assertions.

```php
$authorizationModelId = '01G5JAVJ41T49E9TT3SKVS7X1J';

$client->assertions()->write(
    new WriteAssertionsRequest(
        assertions: [
            new Assertion(
                tuple_key: new AssertionTupleKey(
                    user: 'user:anne',
                    relation: 'viewer',
                    object: 'document:roadmap'
                ),
                expectation: true
            )
            // ... more assertions
        ]
    ),
    $authorizationModelId
);

// Returns void on success, throws exception on failure.
```

```php
use OpenFGA\API\Model\CheckRequestTupleKey;
use OpenFGA\API\Model\CheckRequest;

$response = $fga->check(
  new CheckRequest(
    tuple_key: new CheckRequestTupleKey(
      user: 'user:anne',
      relation: 'reader',
      object: 'document:roadmap',
    ),
    // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
    // context: ['ip_address' => '127.0.0.1'], // optional
  )
);

$allowed = $response->allowed; // true or false
```

```php
use OpenFGA\API\Options\ReadChangesOptions;

// Provide a type for filtering, required by the API
$options = new ReadChangesOptions(type: 'document');

$response = $fga->readChanges(options: $options);

// $response->changes contains an array of TupleChange objects
// $response->continuation_token can be used for pagination

// Example usage:
foreach ($response->changes as $change) {
    $tupleKey = $change->tuple_key;
    $operation = $change->operation; // 'write' or 'delete'
    $timestamp = $change->timestamp;
    // Process the change...
}
```

```php
use OpenFGA\API\Model\ExpandRequestTupleKey;
use OpenFGA\API\Model\ExpandRequest;

$response = $fga->expand(
  new ExpandRequest(
    tuple_key: new ExpandRequestTupleKey(
      relation: 'viewer',
      object: 'document:roadmap'
    ),
    // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
  )
);

// $response->tree contains the UsersetTree
// Process the tree structure...
```

```php
use OpenFGA\API\Model\ListUsersRequest;
use OpenFGA\API\Model\ObjectRelation;

$response = $fga->listUsers(
    new ListUsersRequest(
        object: new \OpenFGA\API\Model\FgaObject(type: 'document', id: 'roadmap'),
        relation: 'viewer',
        user_filters: [
            new \OpenFGA\API\Model\UserTypeFilter(type: 'user') // Filter for users of type 'user'
        ],
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// $response->users contains an array of User objects
// Example:
foreach ($response->users as $user) {
    // $user->object->type, $user->object->id
    // or if userset: $user->userset->type, $user->userset->id, $user->userset->relation
}
```

```php
use OpenFGA\API\Model\ListObjectsRequest;

$response = $fga->listObjects(
    new ListObjectsRequest(
        user: 'user:anne',
        relation: 'viewer',
        type: 'document',
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// $response->objects contains an array of object IDs (strings)
// Example:
foreach ($response->objects as $objectId) {
    // Process the object ID ('document:<id>')
}
```

```php
use OpenFGA\API\Model\StreamedListObjectsRequest;

// Note: Check SDK/HTTP Client documentation for handling streaming responses
$response = $fga->streamedListObjects(
    new StreamedListObjectsRequest(
        user: 'user:anne',
        relation: 'viewer',
        type: 'document',
        // authorization_model_id: '01G5JAVJ41T49E9TT3SKVS7X1J', // optional
        // context: ['ip_address' => '127.0.0.1'], // optional
    )
);

// Process the streaming response...
```

```php
$authorizationModelId = '01G5JAVJ41T49E9TT3SKVS7X1J';

$response = $fga->readAssertions($authorizationModelId);

// $response->assertions is an array of Assertion objects
// Example:
foreach ($response->assertions as $assertion) {
    $tupleKey = $assertion->tuple_key;
    $expectation = $assertion->expectation; // true or false
    // Process assertion...
}
```

```php
$authorizationModelId = '01G5JAVJ41T49E9TT3SKVS7X1J';

$fga->writeAssertions(
    new WriteAssertionsRequest(
        assertions: [
            new Assertion(
                tuple_key: new AssertionTupleKey(
                    user: 'user:anne',
                    relation: 'viewer',
                    object: 'document:roadmap'
                ),
                expectation: true
            )
            // ... more assertions
        ]
    ),
    $authorizationModelId
);

// Returns void on success, throws exception on failure.
```
