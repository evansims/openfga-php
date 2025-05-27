# UsersetTreeDifference


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



#### Returns
[NodeInterface](Models/NodeInterface.md)

### getSubtract


```php
public function getSubtract(): OpenFGA\Models\NodeInterface
```



#### Returns
[NodeInterface](Models/NodeInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetTreeDifferenceInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

