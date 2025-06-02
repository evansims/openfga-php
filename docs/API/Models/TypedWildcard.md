# TypedWildcard

Represents a wildcard that matches all users of a specific type. In authorization models, you sometimes want to grant permissions to all users of a certain type rather than specific individuals. TypedWildcard lets you specify &quot;all users of type X&quot; in your authorization rules. For example, you might want to grant read access to &quot;all employees&quot; or &quot;all customers&quot; without having to list each individual user.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcard.php)

## Implements
* [TypedWildcardInterface](TypedWildcardInterface.md)
* Stringable
* JsonSerializable
* [ModelInterface](ModelInterface.md)

## Related Classes
* [TypedWildcardInterface](Models/TypedWildcardInterface.md) (interface)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `'TypedWildcard'` |  |


## Methods

                                                                        
### List Operations
#### getType


```php
public function getType(): string
```

Get the object type that this wildcard represents. This returns the type name for which the wildcard grants access to all users of that type. For example, &quot;user&quot; would represent all users, &quot;group&quot; would represent all groups, etc.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcard.php#L81)


#### Returns
string
 The object type that this wildcard represents

### Model Management
#### schema

*<small>Implements Models\TypedWildcardInterface</small>*  

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


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TypedWildcard.php#L90)


#### Returns
array

