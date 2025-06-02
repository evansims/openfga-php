# StoreInterface

Represents an OpenFGA store that contains authorization models and relationship tuples. A store is a logical container for all authorization data in OpenFGA, serving as the fundamental organizational unit for authorization systems. Each store contains: - Authorization models that define the permission structure for your application - Relationship tuples that establish actual relationships between users and resources - Configuration and metadata for authorization behavior Stores provide complete isolation between different authorization contexts, making them ideal for multi-tenant applications where different customers, organizations, or environments need separate authorization domains. Each store operates independently with its own set of models, tuples, and access patterns. The store lifecycle includes creation, updates, and optional soft deletion, with comprehensive timestamp tracking for audit and debugging purposes. Stores can be managed through the OpenFGA management API and serve as the target for all authorization queries and relationship operations.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [Store](Models/Store.md) (implementation)



## Methods

                                                                                                            
### CRUD Operations
#### getCreatedAt


```php
public function getCreatedAt(): DateTimeInterface
```

Get the timestamp when the store was created. The creation timestamp provides essential audit information and helps track the lifecycle of authorization stores. This timestamp is set when the store is first created through the OpenFGA API and remains immutable throughout the store&#039;s lifetime.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L44)


#### Returns
DateTimeInterface
 The creation timestamp in UTC timezone

#### getDeletedAt


```php
public function getDeletedAt(): DateTimeInterface|null
```

Get the timestamp when the store was deleted, if applicable. OpenFGA supports soft deletion of stores, allowing them to be marked as deleted while preserving their data for audit and recovery purposes. When a store is soft-deleted, this timestamp records when the deletion occurred. Active stores will return null for this property.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L56)


#### Returns
DateTimeInterface&#124;null
 The deletion timestamp in UTC timezone, or null if the store is active

#### getUpdatedAt


```php
public function getUpdatedAt(): DateTimeInterface
```

Get the timestamp when the store was last updated. The update timestamp tracks when any changes were made to the store&#039;s metadata, such as name changes or configuration updates. This timestamp is automatically maintained by the OpenFGA service and provides important audit information for tracking store modifications over time.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L92)


#### Returns
DateTimeInterface
 The last update timestamp in UTC timezone

### List Operations
#### getId


```php
public function getId(): string
```

Get the unique identifier of the store. The store ID is a globally unique identifier that serves as the primary key for all operations within this authorization context. This ID is used in API requests to target specific stores and ensure isolation between different authorization domains in multi-tenant applications.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L68)


#### Returns
string
 The store&#039;s unique identifier

#### getName


```php
public function getName(): string
```

Get the human-readable name of the store. The store name provides a user-friendly identifier for administrative and debugging purposes. Unlike the store ID, names can be changed and are intended to be meaningful to developers and administrators managing authorization systems. Names help identify stores in dashboards, logs, and management interfaces.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L80)


#### Returns
string
 The store&#039;s display name

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array<'created_at'|'deleted_at'|'id'|'name'|'updated_at', string>
```

Serialize the store for JSON encoding. This method prepares the store data for API communication, converting all properties into a format compatible with the OpenFGA API specification. Timestamps are converted to RFC3339 format in UTC timezone, ensuring consistent date handling across different systems and timezones. The resulting array contains all store properties with their API-compatible names and values, ready for transmission to the OpenFGA service or storage in JSON format.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/StoreInterface.php#L109)


#### Returns
array&lt;'created_at'&#124;'deleted_at'&#124;'id'&#124;'name'&#124;'updated_at', string&gt;
 Store data formatted for JSON encoding with API-compatible field names

