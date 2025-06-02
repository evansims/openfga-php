# Computed

Represents a computed userset reference in authorization evaluation trees. A Computed defines a userset that is calculated based on relationships or other dynamic criteria rather than being explicitly defined. This is used in authorization evaluation trees to represent usersets that are derived through computation during the authorization check process. Use this when working with complex authorization patterns that involve computed or derived user groups.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Computed.php)

## Implements
* [ComputedInterface](ComputedInterface.md)
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Related Classes
* [ComputedInterface](Models/ComputedInterface.md) (interface)
* [Computeds](Models/Collections/Computeds.md) (collection)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `'Computed'` |  |


## Methods

                                                                        
### List Operations
#### getUserset


```php
public function getUserset(): string
```

Get the userset reference string that defines a computed relationship. This represents a reference to another userset that should be computed dynamically based on relationships. The userset string typically follows the format &quot;#relation&quot; to reference a relation on the same object type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Computed.php#L53)


#### Returns
string
 The userset reference string defining the computed relationship

### Model Management
#### schema

*<small>Implements Models\ComputedInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Computed.php#L62)


#### Returns
array

