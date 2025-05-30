# RelationReference


## Namespace
`OpenFGA\Models`

## Implements
* [RelationReferenceInterface](Models/RelationReferenceInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;RelationReference&#039;` |  |


## Methods
### getCondition


```php
public function getCondition(): ?string
```

Get the optional condition name that must be satisfied. When specified, this condition must evaluate to true for the relation reference to be valid. This enables conditional access based on runtime context and attributes.


#### Returns
?string
 The condition name, or null if no condition is required

### getRelation


```php
public function getRelation(): ?string
```

Get the optional specific relation on the referenced type. When specified, this limits the reference to a specific relation on the target type rather than the entire type. This allows for more precise relationship definitions.


#### Returns
?string
 The relation name, or null to reference the entire type

### getType


```php
public function getType(): string
```

Get the type being referenced. This is the object type that this reference points to. It defines which type of objects can be used in relationships through this reference.


#### Returns
string
 The type name being referenced

### getWildcard


```php
public function getWildcard(): ?object
```

Get the optional wildcard marker for type-level permissions. When present, this indicates that the reference applies to all instances of the specified type, rather than specific instances. This is useful for granting permissions at the type level.


#### Returns
?object
 The wildcard marker, or null for instance-specific references

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\RelationReferenceInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

