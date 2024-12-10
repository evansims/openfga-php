# OpenFGA PHP SDK

The `evansims/openfga-php` package is an unofficial/experimental PHP SDK for [OpenFGA](https://openfga.dev/) and [Okta FGA](https://www.okta.com/products/fine-grained-authorization/). It's fast, lightweight, and easy to use.

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
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

## Features

- [OpenFGA](https://openfga.dev/) and [Okta FGA](https://www.okta.com/products/fine-grained-authorization/) are supported
- [Client Credentials flow](https://openfga.dev/docs/getting-started/setup-sdk-client#using-client-credentials-flow) and [shared key authentication](https://openfga.dev/docs/getting-started/setup-sdk-client#using-shared-key-authentication) are supported
- All [API endpoints](https://openfga.dev/api/service) are supported
- Networking is implemented using the [PSR-7](https://www.php-fig.org/psr/psr-7/), [PSR-17](https://www.php-fig.org/psr/psr-17/) and [PSR-18](https://www.php-fig.org/psr/psr-18/) [PHP-FIG](https://www.php-fig.org/) interoperability standards

## Requirements

- [PHP 8.4+](https://www.php.net/)
- [Composer 2](https://getcomposer.org/)
- Access to an [OpenFGA](https://openfga.dev/docs/getting-started/setup-openfga/overview) or [Okta FGA instance](http://dashboard.fga.dev/)

## Installation

```bash
composer require evansims/openfga-php
```

> [!IMPORTANT]
> Your application must fulfill the PSR-7, PSR-17, and PSR-18 implementations. If you're unable to install the SDK due to a missing implementation, first install any libraries of your preference that implement those interfaces, and then retry. For example: `composer require kriswallsmith/buzz nyholm/psr7`.

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
$request = new WriteAuthorizationModelRequest([
  'type_definitions' => [ /* ... */ ],
  'schema_version' => [ /* ... */ ],
  'conditions' => [ /* ... */ ],
]);

$store = $client->store(storeId: 'store-id');
$response = $store->models()->create(
  name: $request
);

echo $response->getId();
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
```

##### Creating a Relationship Tuple

```php
$store = $client->store(storeId: 'store-id');

$store->tuples()->create([
  new TupleKey(
    user: 'user:anne',
    relation: 'writer',
    object: 'document:2021-budget'
  )
]);
```

##### Removing a Relationship Tuple

To remove `user:bob` as a `reader` for `document:2021-budget`, call `write()` with the following:

```php
$store = $client->store(storeId: 'store-id');

$store->tuples()->delete([
  new TupleKey(
    user: 'user:bob',
    relation: 'reader',
    object: 'document:2021-budget'
  )
]);
```

##### Querying Relationship Tuples

###### Getting all `object`s with a `relation`ship to a particular `document`

To query for all objects that `user:bob` has a `reader` relationship with for the `document` type definition:

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

###### Getting all `relation`ships for a particular `object`

To query for all users that have `reader` relationship with `document:2021-budget`:

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

###### Getting all `user`s with `relation`ships to a particular `document`

To query for all users that have `reader` relationship with `document:2021-budget`:

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
