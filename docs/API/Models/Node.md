# Node


## Namespace
`OpenFGA\Models`

## Implements
* `OpenFGA\Models\NodeInterface`
* `JsonSerializable`
* `OpenFGA\Models\ModelInterface`

## Methods
### getDifference

```php
public function getDifference(): ?UsersetTreeDifferenceInterface
```



#### Returns
`?UsersetTreeDifferenceInterface` 

### getIntersection

```php
public function getIntersection(): ?NodeInterface
```



#### Returns
`?NodeInterface` 

### getLeaf

```php
public function getLeaf(): ?LeafInterface
```



#### Returns
`?LeafInterface` 

### getName

```php
public function getName(): string
```



#### Returns
`string` 

### getUnion

```php
public function getUnion(): ?NodeInterface
```



#### Returns
`?NodeInterface` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` 

