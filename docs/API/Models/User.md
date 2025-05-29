# User


## Namespace
`OpenFGA\Models`

## Implements
* [UserInterface](Models/UserInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)



## Methods
### getDifference


```php
public function getDifference(): ?OpenFGA\Models\DifferenceV1Interface
```



#### Returns
?[DifferenceV1Interface](Models/DifferenceV1Interface.md)

### getObject


```php
public function getObject(): ?object
```



#### Returns
?object

### getUserset


```php
public function getUserset(): ?OpenFGA\Models\UsersetUserInterface
```



#### Returns
?[UsersetUserInterface](Models/UsersetUserInterface.md)

### getWildcard


```php
public function getWildcard(): ?OpenFGA\Models\TypedWildcardInterface
```



#### Returns
?[TypedWildcardInterface](Models/TypedWildcardInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UserInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

