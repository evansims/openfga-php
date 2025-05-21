# UsersetTreeTupleToUserset


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Methods
### getComputed


```php
public function getComputed(): [ComputedsInterface](Models/Collections/ComputedsInterface.md)
```



#### Returns
`[ComputedsInterface](Models/Collections/ComputedsInterface.md)` 

### getTupleset


```php
public function getTupleset(): string
```



#### Returns
`string` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` string, computed: array&lt;int, array{userset: string}&gt;}

### schema

*<small>Implements Models\UsersetTreeTupleToUsersetInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

