# TupleOperation

Operations that can be performed on relationship tuples in OpenFGA. This enum defines the available operations for managing relationship tuples through the OpenFGA write API. Tuples represent the actual relationships between users, objects, and relations that form the foundation of all authorization decisions. These operations enable dynamic management of authorization data by adding and removing relationships as your system evolves. Tuple operations are atomic and transactional, ensuring consistency in authorization data. They can be batched together in write requests to perform multiple relationship changes simultaneously, maintaining referential integrity across related permissions. These operations support: - Dynamic permission assignment and revocation - User lifecycle management (onboarding/offboarding) - Role-based access control updates - Temporary access grants and restrictions - Organizational structure changes

## Namespace
`OpenFGA\Models\Enums`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TupleOperation.php)

## Implements
* `UnitEnum`
* `BackedEnum`

## Constants
| Name                     | Value                                                          | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| ------------------------ | -------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `TUPLE_OPERATION_DELETE` | `\OpenFGA\Models\Enums\TupleOperation::TUPLE_OPERATION_DELETE` | Delete operation for removing existing relationship tuples. This operation removes an existing relationship tuple from the authorization store, effectively revoking the relationship between the specified user, object, and relation. The deletion is immediate and will affect subsequent authorization checks that depend on this relationship. Use cases for delete operations: - Revoking user permissions when access should be removed - User offboarding and access cleanup - Removing temporary access grants that have expired - Correcting incorrectly assigned permissions - Organizational changes that require permission updates Delete operations are idempotent - attempting to delete a non-existent tuple will not cause an error, making them safe for cleanup operations and ensuring consistent behavior in distributed environments. |
| `TUPLE_OPERATION_WRITE`  | `\OpenFGA\Models\Enums\TupleOperation::TUPLE_OPERATION_WRITE`  | Write operation for adding new relationship tuples. This operation adds a new relationship tuple to the authorization store, establishing a relationship between the specified user, object, and relation. The new relationship becomes immediately available for authorization checks and will be considered in all relevant permission evaluations. Use cases for write operations: - Granting users new permissions on resources - User onboarding and initial access setup - Dynamic role assignments and promotions - Sharing permissions and delegation scenarios - Creating group memberships and organizational relationships Write operations will overwrite existing tuples with the same key, ensuring that permission grants are idempotent and can be safely repeated without creating duplicate relationships.                                 |

## Cases
| Name                     | Value                    | Description                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  |
| ------------------------ | ------------------------ | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `TUPLE_OPERATION_DELETE` | `TUPLE_OPERATION_DELETE` | Delete operation for removing existing relationship tuples. This operation removes an existing relationship tuple from the authorization store, effectively revoking the relationship between the specified user, object, and relation. The deletion is immediate and will affect subsequent authorization checks that depend on this relationship. Use cases for delete operations: - Revoking user permissions when access should be removed - User offboarding and access cleanup - Removing temporary access grants that have expired - Correcting incorrectly assigned permissions - Organizational changes that require permission updates Delete operations are idempotent - attempting to delete a non-existent tuple will not cause an error, making them safe for cleanup operations and ensuring consistent behavior in distributed environments. |
| `TUPLE_OPERATION_WRITE`  | `TUPLE_OPERATION_WRITE`  | Write operation for adding new relationship tuples. This operation adds a new relationship tuple to the authorization store, establishing a relationship between the specified user, object, and relation. The new relationship becomes immediately available for authorization checks and will be considered in all relevant permission evaluations. Use cases for write operations: - Granting users new permissions on resources - User onboarding and initial access setup - Dynamic role assignments and promotions - Sharing permissions and delegation scenarios - Creating group memberships and organizational relationships Write operations will overwrite existing tuples with the same key, ensuring that permission grants are idempotent and can be safely repeated without creating duplicate relationships.                                 |

## Methods

### List Operations
#### getDescription

```php
public function getDescription(): string
```

Get a user-friendly description of what this operation does. Provides a clear explanation of the operation&#039;s effect on authorization data, useful for logging, auditing, and user interfaces.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TupleOperation.php#L84)

#### Returns
`string` — A descriptive explanation of the operation
### Utility
#### grantsPermissions

```php
public function grantsPermissions(): bool
```

Check if this operation adds permissions to the authorization store. Useful for understanding whether an operation will grant new access or capabilities to users within the system.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TupleOperation.php#L100)

#### Returns
`bool` — True if the operation adds permissions, false otherwise
#### isIdempotent

```php
public function isIdempotent(): true
```

Check if this operation is safe to retry in case of failures. Idempotent operations can be safely retried without causing unintended side effects, making them suitable for retry logic and distributed systems.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TupleOperation.php#L116)

#### Returns
`true` — True if the operation is idempotent, false otherwise
#### revokesPermissions

```php
public function revokesPermissions(): bool
```

Check if this operation removes permissions from the authorization store. Useful for understanding whether an operation will revoke existing access or capabilities from users within the system.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Enums/TupleOperation.php#L129)

#### Returns
`bool` — True if the operation removes permissions, false otherwise
