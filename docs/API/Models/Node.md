# Node


## Namespace
`OpenFGA\Models`

## Implements
* [NodeInterface](Models/NodeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Methods
### getDifference

```php
public function getDifference(): ?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)
```



#### Returns
`?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)` 

### getIntersection

```php
public function getIntersection(): ?[NodeInterface](Models/NodeInterface.md)
```



#### Returns
`?[NodeInterface](Models/NodeInterface.md)` 

### getLeaf

```php
public function getLeaf(): ?[LeafInterface](Models/LeafInterface.md)
```



#### Returns
`?[LeafInterface](Models/LeafInterface.md)` 

### getName

```php
public function getName(): string
```



#### Returns
`string` 

### getUnion

```php
public function getUnion(): ?[NodeInterface](Models/NodeInterface.md)
```



#### Returns
`?[NodeInterface](Models/NodeInterface.md)` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` 

