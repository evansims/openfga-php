# TupleToUsersetV1


## Namespace
`OpenFGA\Models`

## Implements
* [TupleToUsersetV1Interface](Models/TupleToUsersetV1Interface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_TYPE` | `&#039;v1.TupleToUserset&#039;` |  |


## Methods
### getComputedUserset


```php
public function getComputedUserset(): OpenFGA\Models\ObjectRelationInterface
```



#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md)

### getTupleset


```php
public function getTupleset(): OpenFGA\Models\ObjectRelationInterface
```



#### Returns
[ObjectRelationInterface](Models/ObjectRelationInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TupleToUsersetV1Interface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

