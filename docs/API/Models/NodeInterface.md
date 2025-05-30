# NodeInterface

Represents a node in a userset tree structure. Nodes are fundamental building blocks in OpenFGA&#039;s authorization model that represent different types of relationships and operations within the access control evaluation tree.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



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
public function getIntersection(): NodeUnionInterface|self|null
```

Get the intersection operation for this node. The intersection operation represents the common elements between multiple usersets in the authorization tree.


#### Returns
[NodeUnionInterface](Models/NodeUnionInterface.md) | self | null
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
public function getUnion(): NodeUnionInterface|self|null
```

Get the union operation for this node. The union operation represents the combination of multiple usersets where users from any of the sets are included in the result.


#### Returns
[NodeUnionInterface](Models/NodeUnionInterface.md) | self | null
 The union node or null if not applicable

### jsonSerialize


```php
public function jsonSerialize(): array
```

Serialize the node to its JSON representation.


#### Returns
array

