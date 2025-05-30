# UserObject


## Namespace
`OpenFGA\Models`

## Implements
* [UserObjectInterface](Models/UserObjectInterface.md)
* Stringable
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UserObject&#039;` |  |


## Methods
### getId


```php
public function getId(): string
```

Get the unique identifier of the user object. The ID is unique within the context of the object type and represents the specific instance of the typed object.


#### Returns
string
 The object identifier

### getType


```php
public function getType(): string
```

Get the type of the user object. The type defines the category or class of the object (e.g., &#039;user&#039;, &#039;group&#039;, &#039;organization&#039;) and must be defined in the authorization model.


#### Returns
string
 The object type

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the user object to its JSON representation.


#### Returns
array

### schema

*<small>Implements Models\UserObjectInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

