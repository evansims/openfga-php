# LeafInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getComputed


```php
public function getComputed(): ?[ComputedInterface](Models/ComputedInterface.md)
```



#### Returns
`?[ComputedInterface](Models/ComputedInterface.md)`

### getTupleToUserset


```php
public function getTupleToUserset(): ?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)
```



#### Returns
`?[UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)`

### getUsers


```php
public function getUsers(): null | UsersListInterface<UsersListUserInterface>
```



#### Returns
`null | UsersListInterface<UsersListUserInterface>`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

