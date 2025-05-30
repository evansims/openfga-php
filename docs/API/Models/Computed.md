# Computed


## Namespace
`OpenFGA\Models`

## Implements
* [ComputedInterface](Models/ComputedInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Computed&#039;` |  |


## Methods
### getUserset


```php
public function getUserset(): string
```

Get the userset reference string that defines a computed relationship. This represents a reference to another userset that should be computed dynamically based on relationships. The userset string typically follows the format &quot;#relation&quot; to reference a relation on the same object type.


#### Returns
string
 The userset reference string defining the computed relationship

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\ComputedInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

