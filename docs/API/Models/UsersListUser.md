# UsersListUser


## Namespace
`OpenFGA\Models`

## Implements
* [UsersListUserInterface](Models/UsersListUserInterface.md)
* Stringable
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersListUser&#039;` |  |


## Methods
### getUser


```php
public function getUser(): string
```

Get the user identifier string. This returns the user identifier in the format expected by OpenFGA, typically &quot;type:id&quot; where type describes the kind of user and id is the unique identifier for that user.


#### Returns
string
 The user identifier string

### jsonSerialize


```php
public function jsonSerialize(): string
```



#### Returns
string

### schema

*<small>Implements Models\UsersListUserInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

