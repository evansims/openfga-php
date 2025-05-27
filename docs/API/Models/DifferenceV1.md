# DifferenceV1


## Namespace
`OpenFGA\Models`

## Implements
* [DifferenceV1Interface](Models/DifferenceV1Interface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;v1.Difference&#039;` |  |


## Methods
### getBase


```php
public function getBase(): [UsersetInterface](Models/UsersetInterface.md)
```



#### Returns
`[UsersetInterface](Models/UsersetInterface.md)`

### getSubtract


```php
public function getSubtract(): [UsersetInterface](Models/UsersetInterface.md)
```



#### Returns
`[UsersetInterface](Models/UsersetInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

### schema

*<small>Implements Models\DifferenceV1Interface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

