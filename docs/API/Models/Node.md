# Node

Represents a node in the authorization evaluation tree structure. When OpenFGA evaluates complex authorization rules, it builds a tree of nodes representing different evaluation paths. Each node can contain unions, intersections, differences, or leaf computations that contribute to the final authorization decision. This is the fundamental building block for representing how authorization decisions are computed and provides insight into the evaluation process.

## Namespace
`OpenFGA\Models`

## Implements
* [NodeInterface](Models/NodeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Node&#039;` |  |


## Methods
### getDifference


```php
public function getDifference(): ?OpenFGA\Models\UsersetTreeDifferenceInterface
```

Get the difference operation for this node. The difference operation represents a set subtraction where users from one set are excluded from another set.


#### Returns
?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)

### getIntersection


```php
public function getIntersection(): ?OpenFGA\Models\NodeInterface|OpenFGA\Models\NodeUnionInterface|null
```

Get the intersection operation for this node. The intersection operation represents the common elements between multiple usersets in the authorization tree.


#### Returns
?[NodeInterface](Models/NodeInterface.md) | [NodeUnionInterface](Models/NodeUnionInterface.md) | null
 The intersection node or null if not applicable

### getLeaf


```php
public function getLeaf(): ?OpenFGA\Models\LeafInterface
```

Get the leaf node if this is a terminal node. Leaf nodes represent the actual users, computed usersets, or tuple-to-userset relationships at the end of the evaluation tree.


#### Returns
?[LeafInterface](Models/LeafInterface.md)

### getName


```php
public function getName(): string
```

Get the name identifier for this node. The name is used to identify the node within the authorization model and corresponds to relation names or other identifiers.


#### Returns
string
 The node name

### getUnion


```php
public function getUnion(): ?OpenFGA\Models\NodeInterface|OpenFGA\Models\NodeUnionInterface|null
```

Get the union operation for this node. The union operation represents the combination of multiple usersets where users from any of the sets are included in the result.


#### Returns
?[NodeInterface](Models/NodeInterface.md) | [NodeUnionInterface](Models/NodeUnionInterface.md) | null
 The union node or null if not applicable

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the node to its JSON representation.


#### Returns
array
 The serialized node data

### schema

*<small>Implements Models\NodeInterface</small>*  

```php
public function schema(): SchemaInterface
```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.


#### Returns
SchemaInterface
 The schema definition containing validation rules and property specifications for this model

