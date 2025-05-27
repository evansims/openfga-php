# Userset


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetInterface](Models/UsersetInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_TYPE` | `&#039;Userset&#039;` |  |


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
public function getIntersection(): ?[UsersetsInterface](Models/Collections/UsersetsInterface.md)
```



#### Returns
`?[UsersetsInterface](Models/Collections/UsersetsInterface.md)`

### getTupleToUserset


```php
public function getTupleToUserset(): ?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)
```



#### Returns
`?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)`

### getUnion


```php
public function getUnion(): ?[UsersetsInterface](Models/Collections/UsersetsInterface.md)
```



#### Returns
`?[UsersetsInterface](Models/Collections/UsersetsInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

### schema

*<small>Implements Models\UsersetInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

