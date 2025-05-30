# DifferenceV1


## Namespace
`OpenFGA\Models`

## Implements
* [DifferenceV1Interface](Models/DifferenceV1Interface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;v1.Difference&#039;` |  |


## Methods
### getBase


```php
public function getBase(): OpenFGA\Models\UsersetInterface
```

Get the base userset from which users will be subtracted. This represents the initial set of users or relationships from which the subtract userset will be removed to compute the final difference.


#### Returns
[UsersetInterface](Models/UsersetInterface.md)
 The base userset for the difference operation

### getSubtract


```php
public function getSubtract(): OpenFGA\Models\UsersetInterface
```

Get the userset of users to subtract from the base userset. This represents the set of users or relationships that should be removed from the base userset to compute the final result of the difference operation.


#### Returns
[UsersetInterface](Models/UsersetInterface.md)
 The userset to subtract from the base

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\DifferenceV1Interface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

