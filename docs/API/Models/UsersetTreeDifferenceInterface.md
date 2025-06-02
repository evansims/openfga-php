# UsersetTreeDifferenceInterface

Defines a difference operation node in authorization evaluation trees. UsersetTreeDifference represents a node in the userset evaluation tree that computes the difference between two child nodes, effectively calculating &quot;users in base except those in subtract&quot;. This enables authorization patterns where access is granted to one group while explicitly excluding another. Use this interface when working with authorization evaluation trees that contain difference operations, typically returned from expand operations.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getBase


```php
public function getBase(): NodeInterface
```

Get the base node from which the subtract node will be removed. This represents the initial node in the userset tree from which users will be subtracted to compute the final difference result.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The base node for the difference operation

### getSubtract


```php
public function getSubtract(): NodeInterface
```

Get the node representing users to subtract from the base. This represents the node in the userset tree whose users should be removed from the base node to compute the final difference result.


#### Returns
[NodeInterface](Models/NodeInterface.md)
 The node to subtract from the base

### jsonSerialize


```php
public function jsonSerialize(): array<string, mixed>
```



#### Returns
array&lt;string, mixed&gt;

