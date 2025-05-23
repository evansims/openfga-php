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
public function getBase(): [NodeInterface](Models/NodeInterface.md)
```



#### Returns
`[NodeInterface](Models/NodeInterface.md)`

### getSubtract


```php
public function getSubtract(): [NodeInterface](Models/NodeInterface.md)
```



#### Returns
`[NodeInterface](Models/NodeInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 array{name: string, leaf?: array{users?: array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}, subtract: array{name: string, leaf?: array{users?: array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}}

### schema

*<small>Implements Models\UsersetTreeDifferenceInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

