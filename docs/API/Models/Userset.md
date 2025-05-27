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
public function getIntersection(): ?OpenFGA\Models\Collections\UsersetsInterface
```



#### Returns
?[UsersetsInterface](Models/Collections/UsersetsInterface.md)

### getTupleToUserset


```php
public function getTupleToUserset(): ?OpenFGA\Models\TupleToUsersetV1Interface
```



#### Returns
?[TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)

### getUnion


```php
public function getUnion(): ?OpenFGA\Models\Collections\UsersetsInterface
```



#### Returns
?[UsersetsInterface](Models/Collections/UsersetsInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

