# RelationReference

Represents a reference to a specific relation with optional conditions. A RelationReference identifies a relation within your authorization model, optionally with an associated condition that must be satisfied. This enables conditional relationships where the relation is only valid when certain runtime conditions are met. Use this when defining relation constraints or implementing attribute-based access control patterns in your authorization model.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php)

## Implements
* [`RelationReferenceInterface`](RelationReferenceInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes
* [RelationReferenceInterface](Models/RelationReferenceInterface.md) (interface)
* [RelationReferences](Models/Collections/RelationReferences.md) (collection)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `'RelationReference'` |  |

## Methods

### List Operations
#### getCondition

```php
public function getCondition(): ?string
```

Get the optional condition name that must be satisfied. When specified, this condition must evaluate to true for the relation reference to be valid. This enables conditional access based on runtime context and attributes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php#L62)

#### Returns
`string` &#124; `null` — The condition name, or null if no condition is required
#### getRelation

```php
public function getRelation(): ?string
```

Get the optional specific relation on the referenced type. When specified, this limits the reference to a specific relation on the target type rather than the entire type. This allows for more precise relationship definitions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php#L71)

#### Returns
`string` &#124; `null` — The relation name, or null to reference the entire type
#### getType

```php
public function getType(): string
```

Get the type being referenced. This is the object type that this reference points to. It defines which type of objects can be used in relationships through this reference.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php#L80)

#### Returns
`string` — The type name being referenced
#### getWildcard

```php
public function getWildcard(): ?object
```

Get the optional wildcard marker for type-level permissions. When present, this indicates that the reference applies to all instances of the specified type, rather than specific instances. This is useful for granting permissions at the type level.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php#L89)

#### Returns
`object` &#124; `null` — The wildcard marker, or null for instance-specific references
### Model Management
#### schema

*<small>Implements Models\RelationReferenceInterface</small>*

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
public function jsonSerialize(): array
```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReference.php#L98)

#### Returns
`array`
