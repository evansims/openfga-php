# UsersetTreeDifference

Represents a difference operation node in authorization evaluation trees. UsersetTreeDifference computes the difference between two nodes in the authorization evaluation tree, effectively calculating &quot;users in base except those in subtract&quot;. This enables complex authorization patterns where access is granted to one group while explicitly excluding another. Use this when working with authorization evaluation trees that involve set difference operations.

## Namespace
`OpenFGA\Models`

## Implements
* [UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersetTree.Difference&#039;` |  |


## Methods
### getBase


```php
public function getBase(): OpenFGA\Models\NodeInterface
```

Get the base node from which the subtract node will be removed. This represents the initial node in the userset tree from which users will be subtracted to compute the final difference result.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The base node for the difference operation

### getSubtract


```php
public function getSubtract(): OpenFGA\Models\NodeInterface
```

Get the node representing users to subtract from the base. This represents the node in the userset tree whose users should be removed from the base node to compute the final difference result.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The node to subtract from the base

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetTreeDifferenceInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

