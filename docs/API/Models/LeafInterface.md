# LeafInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



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
public function getUsers(): null|UsersListInterface<UsersListUserInterface>
```



#### Returns
null | UsersListInterface&lt;[UsersListUserInterface](Models/UsersListUserInterface.md)&gt;

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

