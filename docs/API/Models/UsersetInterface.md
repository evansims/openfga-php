# UsersetInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputedUserset


```php
public function getComputedUserset(): ?OpenFGA\Models\ObjectRelationInterface
```



#### Returns
?[ObjectRelationInterface](Models/ObjectRelationInterface.md)

### getDifference


```php
public function getDifference(): ?OpenFGA\Models\DifferenceV1Interface
```



#### Returns
?[DifferenceV1Interface](Models/DifferenceV1Interface.md)

### getDirect


```php
public function getDirect(): ?object
```



#### Returns
?object

### getIntersection


```php
public function getIntersection(): null|UsersetsInterface<UsersetInterface>
```



#### Returns
null | UsersetsInterface&lt;[UsersetInterface](Models/UsersetInterface.md)&gt;

### getTupleToUserset


```php
public function getTupleToUserset(): ?OpenFGA\Models\TupleToUsersetV1Interface
```



#### Returns
?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)

### getUnion


```php
public function getUnion(): null|UsersetsInterface<UsersetInterface>
```



#### Returns
null | UsersetsInterface&lt;[UsersetInterface](Models/UsersetInterface.md)&gt;

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

