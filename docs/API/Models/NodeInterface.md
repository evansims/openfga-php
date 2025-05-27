# NodeInterface


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



#### Returns
?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)

### getIntersection


```php
public function getIntersection(): ?self
```



#### Returns
?self

### getLeaf


```php
public function getLeaf(): ?OpenFGA\Models\LeafInterface
```



#### Returns
?[LeafInterface](Models/LeafInterface.md)

### getName


```php
public function getName(): string
```



#### Returns
string

### getUnion


```php
public function getUnion(): ?self
```



#### Returns
?self

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

