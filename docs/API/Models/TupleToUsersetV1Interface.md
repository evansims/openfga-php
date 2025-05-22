# TupleToUsersetV1Interface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputedUserset


```php
public function getComputedUserset(): [ObjectRelationInterface](Models/ObjectRelationInterface.md)
```



#### Returns
`[ObjectRelationInterface](Models/ObjectRelationInterface.md)`

### getTupleset


```php
public function getTupleset(): [ObjectRelationInterface](Models/ObjectRelationInterface.md)
```



#### Returns
`[ObjectRelationInterface](Models/ObjectRelationInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 array{object?: string, relation?: string}, computed_userset: array{object?: string, relation?: string}}

