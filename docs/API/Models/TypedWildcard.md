# TypedWildcard


## Namespace
`OpenFGA\Models`

## Implements
* [TypedWildcardInterface](Models/TypedWildcardInterface.md)
* Stringable
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;TypedWildcard&#039;` |  |


## Methods
### getType


```php
public function getType(): string
```

Get the object type that this wildcard represents. This returns the type name for which the wildcard grants access to all users of that type. For example, &quot;user&quot; would represent all users, &quot;group&quot; would represent all groups, etc.


#### Returns
string
 The object type that this wildcard represents

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TypedWildcardInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

