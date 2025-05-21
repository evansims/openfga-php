# NodeInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getDifference


```php
public function getDifference(): ?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)
```



#### Returns
`?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)` 

### getIntersection


```php
public function getIntersection(): ?self
```



#### Returns
`?self` 

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
public function getUnion(): ?self
```



#### Returns
`?self` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` string, leaf?: array{users?: array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}

