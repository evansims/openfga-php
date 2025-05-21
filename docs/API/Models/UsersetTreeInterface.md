# UsersetTreeInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

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
`array` array{name: string, leaf?: array{users?: array&lt;int, string&gt;, computed?: array{userset: string}, tupleToUserset?: mixed}, difference?: mixed, intersection?: mixed, union?: mixed}}

