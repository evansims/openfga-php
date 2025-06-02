# UsersListUser

Represents a user entry in a users list response. UsersListUser provides a simple wrapper around user identifiers returned from list operations. It ensures consistent representation of users in lists while providing convenient access to the user identifier string. Use this when working with user lists returned from OpenFGA queries or when you need a structured representation of user identifiers.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersListUser.php)

## Implements
* [`UsersListUserInterface`](UsersListUserInterface.md)
* `Stringable`
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes
* [UsersListUserInterface](Models/UsersListUserInterface.md) (interface)

## Constants
| Name            | Value             | Description |
| --------------- | ----------------- | ----------- |
| `OPENAPI_MODEL` | `'UsersListUser'` |             |

## Methods

### List Operations
#### getUser

```php
public function getUser(): string
```

Get the user identifier string. This returns the user identifier in the format expected by OpenFGA, typically &quot;type:id&quot; where type describes the kind of user and id is the unique identifier for that user.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersListUser.php#L65)

#### Returns
`string` — The user identifier string
### Model Management
#### schema

*<small>Implements Models\UsersListUserInterface</small>*

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
public function jsonSerialize(): string
```

Serialize the user to its JSON representation. Returns the user identifier as a string for API serialization. This differs from most models which serialize to arrays.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/UsersListUser.php#L74)

#### Returns
`string` — The user identifier string
