# Store

Represents an OpenFGA authorization store that contains your permission data. A Store is a container for all your authorization data - the models, relationships, and permission rules for a specific application or tenant. Each store is isolated from others, allowing you to manage permissions for different applications or environments separately. Think of a store as your application&#039;s dedicated permission database that holds all the &quot;who can do what&quot; information for your system.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php)

## Implements

* [`StoreInterface`](StoreInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [StoreInterface](Models/StoreInterface.md) (interface)
* [Stores](Models/Collections/Stores.md) (collection)

## Constants

| Name            | Value     | Description |
| --------------- | --------- | ----------- |
| `OPENAPI_MODEL` | `'Store'` |             |

## Methods

### CRUD Operations

#### getCreatedAt

```php
public function getCreatedAt(): DateTimeInterface

```

Get the timestamp when the store was created. The creation timestamp provides essential audit information and helps track the lifecycle of authorization stores. This timestamp is set when the store is first created through the OpenFGA API and remains immutable throughout the store&#039;s lifetime.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L68)

#### Returns

`DateTimeInterface` — The creation timestamp in UTC timezone

#### getDeletedAt

```php
public function getDeletedAt(): ?DateTimeInterface

```

Get the timestamp when the store was deleted, if applicable. OpenFGA supports soft deletion of stores, allowing them to be marked as deleted while preserving their data for audit and recovery purposes. When a store is soft-deleted, this timestamp records when the deletion occurred. Active stores will return null for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L77)

#### Returns

`DateTimeInterface` &#124; `null` — The deletion timestamp in UTC timezone, or null if the store is active

#### getUpdatedAt

```php
public function getUpdatedAt(): DateTimeInterface

```

Get the timestamp when the store was last updated. The update timestamp tracks when any changes were made to the store&#039;s metadata, such as name changes or configuration updates. This timestamp is automatically maintained by the OpenFGA service and provides important audit information for tracking store modifications over time.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L104)

#### Returns

`DateTimeInterface` — The last update timestamp in UTC timezone

### List Operations

#### getId

```php
public function getId(): string

```

Get the unique identifier of the store. The store ID is a globally unique identifier that serves as the primary key for all operations within this authorization context. This ID is used in API requests to target specific stores and ensure isolation between different authorization domains in multi-tenant applications.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L86)

#### Returns

`string` — The store&#039;s unique identifier

#### getName

```php
public function getName(): string

```

Get the human-readable name of the store. The store name provides a user-friendly identifier for administrative and debugging purposes. Unlike the store ID, names can be changed and are intended to be meaningful to developers and administrators managing authorization systems. Names help identify stores in dashboards, logs, and management interfaces.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L95)

#### Returns

`string` — The store&#039;s display name

### Model Management

#### schema

*<small>Implements Models\StoreInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

Serialize the store for JSON encoding. This method prepares the store data for API communication, converting all properties into a format compatible with the OpenFGA API specification. Timestamps are converted to RFC3339 format in UTC timezone, ensuring consistent date handling across different systems and timezones. The resulting array contains all store properties with their API-compatible names and values, ready for transmission to the OpenFGA service or storage in JSON format.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Store.php#L113)

#### Returns

`array` — Store data formatted for JSON encoding with API-compatible field names
