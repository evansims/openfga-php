# Userset


## Namespace
`OpenFGA\Models`

## Implements
* `OpenFGA\Models\UsersetInterface`
* `JsonSerializable`
* `OpenFGA\Models\ModelInterface`

## Methods
### getComputedUserset

```php
public function getComputedUserset(): ?ObjectRelationInterface
```



#### Returns
`?ObjectRelationInterface` 

### getDifference

```php
public function getDifference(): ?DifferenceV1Interface
```



#### Returns
`?DifferenceV1Interface` 

### getDirect

```php
public function getDirect(): ?object
```



#### Returns
`?object` 

### getIntersection

```php
public function getIntersection(): ?UsersetsInterface
```



#### Returns
`?UsersetsInterface` 

### getTupleToUserset

```php
public function getTupleToUserset(): ?TupleToUsersetV1Interface
```



#### Returns
`?TupleToUsersetV1Interface` 

### getUnion

```php
public function getUnion(): ?UsersetsInterface
```



#### Returns
`?UsersetsInterface` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` 

