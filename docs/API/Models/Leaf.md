# Leaf


## Namespace
`OpenFGA\Models`

## Implements
* [LeafInterface](Models/LeafInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Leaf&#039;` |  |


## Methods
### getComputed


```php
public function getComputed(): ?OpenFGA\Models\ComputedInterface
```



#### Returns
?[ComputedInterface](Models/ComputedInterface.md)

### getTupleToUserset


```php
public function getTupleToUserset(): ?OpenFGA\Models\UsersetTreeTupleToUsersetInterface
```



#### Returns
?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)

### getUsers


```php
public function getUsers(): ?OpenFGA\Models\Collections\UsersListInterface
```



#### Returns
?[UsersListInterface](Models/Collections/UsersListInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\LeafInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

