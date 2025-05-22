# UsersetTree


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetTreeInterface](Models/UsersetTreeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_TYPE` | `&#039;UsersetTree&#039;` |  |


## Methods
### getRoot


```php
public function getRoot(): [NodeInterface](Models/NodeInterface.md)
```



#### Returns
`[NodeInterface](Models/NodeInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 array{name: string, leaf?: array{users?: array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}}

### schema

*<small>Implements Models\UsersetTreeInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

