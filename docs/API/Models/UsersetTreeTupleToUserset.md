# UsersetTreeTupleToUserset


## Namespace
`OpenFGA\Models`

## Implements
* [UsersetTreeTupleToUsersetInterface](Models/UsersetTreeTupleToUsersetInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;UsersetTree.TupleToUserset&#039;` |  |


## Methods
### getComputed


```php
public function getComputed(): OpenFGA\Models\Collections\ComputedsInterface
```



#### Returns
[ComputedsInterface](Models/Collections/ComputedsInterface.md)

### getTupleset


```php
public function getTupleset(): string
```



#### Returns
string

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\UsersetTreeTupleToUsersetInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

