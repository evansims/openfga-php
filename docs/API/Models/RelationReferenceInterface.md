# RelationReferenceInterface

Defines the contract for relation references with optional conditions. A relation reference specifies a particular relation within a type definition, optionally with an associated condition that must be satisfied. This allows for conditional access patterns where relationships are only valid when certain runtime conditions are met. Use this when you need to reference specific relations in your authorization model, especially when implementing attribute-based access control (ABAC) patterns.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getCondition


```php
public function getCondition(): string|null
```

Get the optional condition name that must be satisfied. When specified, this condition must evaluate to true for the relation reference to be valid. This enables conditional access based on runtime context and attributes.


#### Returns
string | null
 The condition name, or null if no condition is required

### getRelation


```php
public function getRelation(): string|null
```

Get the optional specific relation on the referenced type. When specified, this limits the reference to a specific relation on the target type rather than the entire type. This allows for more precise relationship definitions.


#### Returns
string | null
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
public function getWildcard(): object|null
```

Get the optional wildcard marker for type-level permissions. When present, this indicates that the reference applies to all instances of the specified type, rather than specific instances. This is useful for granting permissions at the type level.


#### Returns
object | null
 The wildcard marker, or null for instance-specific references

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

