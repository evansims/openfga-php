# UsersetInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputedUserset


```php
public function getComputedUserset(): ?[ObjectRelationInterface](Models/ObjectRelationInterface.md)
```



#### Returns
`?[ObjectRelationInterface](Models/ObjectRelationInterface.md)`

### getDifference


```php
public function getDifference(): ?[DifferenceV1Interface](Models/DifferenceV1Interface.md)
```



#### Returns
`?[DifferenceV1Interface](Models/DifferenceV1Interface.md)`

### getDirect


```php
public function getDirect(): ?object
```



#### Returns
`?object`

### getIntersection


```php
public function getIntersection(): null | UsersetsInterface<UsersetInterface>
```



#### Returns
`null | UsersetsInterface<UsersetInterface>`

### getTupleToUserset


```php
public function getTupleToUserset(): ?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)
```



#### Returns
`?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)`

### getUnion


```php
public function getUnion(): null | UsersetsInterface<UsersetInterface>
```



#### Returns
`null | UsersetsInterface<UsersetInterface>`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

