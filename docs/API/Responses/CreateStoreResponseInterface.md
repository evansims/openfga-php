# CreateStoreResponseInterface

Interface for store creation response objects. This interface defines the contract for responses returned when creating new authorization stores in OpenFGA. A store creation response contains the newly created store&#039;s metadata including its unique identifier, name, and timestamps. Store creation is the foundational operation for establishing an authorization domain where you can define relationship models, write authorization tuples, and perform permission checks.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [CRUD Operations](#crud-operations)
  - [`getCreatedAt()`](#getcreatedat)
  - [`getUpdatedAt()`](#getupdatedat)
- [List Operations](#list-operations)
  - [`getId()`](#getid)
  - [`getName()`](#getname)

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php)

## Implements

- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [CreateStoreResponse](Responses/CreateStoreResponse.md) (implementation)
- [CreateStoreRequestInterface](Requests/CreateStoreRequestInterface.md) (request)

## Methods

### CRUD Operations

#### getCreatedAt

```php
public function getCreatedAt(): DateTimeImmutable

```

Get the timestamp when the store was created. Returns the exact moment when the store was successfully created in the OpenFGA system. This timestamp is immutable and set by the server upon store creation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php#L42)

#### Returns

`DateTimeImmutable` — The creation timestamp of the store

#### getUpdatedAt

```php
public function getUpdatedAt(): DateTimeImmutable

```

Get the timestamp when the store was last updated. Returns the timestamp of the most recent modification to the store&#039;s metadata. For newly created stores, this will typically match the creation timestamp.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php#L72)

#### Returns

`DateTimeImmutable` — The last update timestamp of the store

### List Operations

#### getId

```php
public function getId(): string

```

Get the unique identifier of the created store. Returns the system-generated unique identifier for the newly created store. This ID is used in all subsequent API operations to reference this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php#L52)

#### Returns

`string` — The unique store identifier

#### getName

```php
public function getName(): string

```

Get the human-readable name of the created store. Returns the descriptive name that was assigned to the store during creation. This name is used for identification and administrative purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/CreateStoreResponseInterface.php#L62)

#### Returns

`string` — The descriptive name of the store
