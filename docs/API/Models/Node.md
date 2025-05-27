# Node


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



#### Returns
?[UsersetTreeDifferenceInterface](Models/UsersetTreeDifferenceInterface.md)

### getIntersection


```php
public function getIntersection(): ?OpenFGA\Models\NodeInterface
```



#### Returns
?[NodeInterface](Models/NodeInterface.md)

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
public function getUnion(): ?OpenFGA\Models\NodeInterface
```



#### Returns
?[NodeInterface](Models/NodeInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\NodeInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

