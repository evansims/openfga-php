# User


## Namespace
`OpenFGA\Models`

## Implements
* [UserInterface](Models/UserInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)



## Methods
### getObject


```php
public function getObject(): ?object
```



#### Returns
`?object`

### getUserset


```php
public function getUserset(): ?[UsersetUserInterface](Models/UsersetUserInterface.md)
```



#### Returns
`?[UsersetUserInterface](Models/UsersetUserInterface.md)`

### getWildcard


```php
public function getWildcard(): ?[TypedWildcardInterface](Models/TypedWildcardInterface.md)
```



#### Returns
`?[TypedWildcardInterface](Models/TypedWildcardInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

### schema

*<small>Implements Models\UserInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

