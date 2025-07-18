# GetStoreResponseInterface

Interface for store retrieval response objects. This interface defines the contract for responses returned when retrieving store information from OpenFGA. A store retrieval response contains comprehensive metadata about the store including its identifier, name, timestamps, and full store object. Store retrieval is useful for administrative operations, auditing, and displaying store information in management interfaces.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getCreatedAt()`](#getcreatedat)
  - [`getDeletedAt()`](#getdeletedat)
  - [`getId()`](#getid)
  - [`getName()`](#getname)
  - [`getStore()`](#getstore)
  - [`getUpdatedAt()`](#getupdatedat)

</details>

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php)

## Implements

- [`ResponseInterface`](ResponseInterface.md)

## Related Classes

- [GetStoreResponse](Responses/GetStoreResponse.md) (implementation)
- [GetStoreRequestInterface](Requests/GetStoreRequestInterface.md) (request)

## Methods

### getCreatedAt

```php
public function getCreatedAt(): DateTimeImmutable

```

Get the timestamp when the store was created. Returns the exact moment when the store was successfully created in the OpenFGA system. This timestamp is immutable and set by the server upon store creation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L44)

#### Returns

`DateTimeImmutable` — The creation timestamp of the store

### getDeletedAt

```php
public function getDeletedAt(): DateTimeImmutable|null

```

Get the timestamp when the store was deleted, if applicable. Returns the deletion timestamp for soft-deleted stores, or null if the store is active. This is used for stores that have been marked for deletion but may still be accessible for a grace period.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L55)

#### Returns

`DateTimeImmutable` &#124; `null` — The deletion timestamp, or null if the store is not deleted

### getId

```php
public function getId(): string

```

Get the unique identifier of the store. Returns the system-generated unique identifier for the store. This ID is used in all API operations to reference this specific store.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L65)

#### Returns

`string` — The unique store identifier

### getName

```php
public function getName(): string

```

Get the human-readable name of the store. Returns the descriptive name that was assigned to the store during creation or last update. This name is used for identification and administrative purposes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L75)

#### Returns

`string` — The descriptive name of the store

### getStore

```php
public function getStore(): StoreInterface

```

Get the complete store object. Returns the full store object containing all store metadata and configuration. This provides access to the complete store data structure including any additional properties beyond the individual accessor methods.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L86)

#### Returns

[`StoreInterface`](Models/StoreInterface.md) — The complete store object

### getUpdatedAt

```php
public function getUpdatedAt(): DateTimeImmutable

```

Get the timestamp when the store was last updated. Returns the timestamp of the most recent modification to the store&#039;s metadata or configuration. This is updated whenever store properties are changed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/GetStoreResponseInterface.php#L96)

#### Returns

`DateTimeImmutable` — The last update timestamp of the store
