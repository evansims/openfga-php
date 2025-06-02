# RelationReferenceInterface

Defines the contract for relation references with optional conditions. A relation reference specifies a particular relation within a type definition, optionally with an associated condition that must be satisfied. This allows for conditional access patterns where relationships are only valid when certain runtime conditions are met. Use this when you need to reference specific relations in your authorization model, especially when implementing attribute-based access control (ABAC) patterns.

## Namespace
`OpenFGA\Models`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php)

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable

## Related Classes
* [RelationReference](Models/RelationReference.md) (implementation)



## Methods

                                                                                    
### List Operations
#### getCondition


```php
public function getCondition(): string|null
```

Get the optional condition name that must be satisfied. When specified, this condition must evaluate to true for the relation reference to be valid. This enables conditional access based on runtime context and attributes.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php#L31)


#### Returns
string&#124;null
 The condition name, or null if no condition is required

#### getRelation


```php
public function getRelation(): string|null
```

Get the optional specific relation on the referenced type. When specified, this limits the reference to a specific relation on the target type rather than the entire type. This allows for more precise relationship definitions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php#L42)


#### Returns
string&#124;null
 The relation name, or null to reference the entire type

#### getType


```php
public function getType(): string
```

Get the type being referenced. This is the object type that this reference points to. It defines which type of objects can be used in relationships through this reference.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php#L52)


#### Returns
string
 The type name being referenced

#### getWildcard


```php
public function getWildcard(): object|null
```

Get the optional wildcard marker for type-level permissions. When present, this indicates that the reference applies to all instances of the specified type, rather than specific instances. This is useful for granting permissions at the type level.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php#L63)


#### Returns
object&#124;null
 The wildcard marker, or null for instance-specific references

### Other
#### jsonSerialize


```php
public function jsonSerialize(): array
```


[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/RelationReferenceInterface.php#L69)


#### Returns
array

