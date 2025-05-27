# TupleKey


## Namespace
`OpenFGA\Models`

## Implements
* [TupleKeyInterface](Models/TupleKeyInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;TupleKey&#039;` |  |


## Methods
### getCondition


```php
public function getCondition(): ?OpenFGA\Models\ConditionInterface
```



#### Returns
?[ConditionInterface](Models/ConditionInterface.md)

### getObject


```php
public function getObject(): string
```



#### Returns
string

### getRelation


```php
public function getRelation(): string
```



#### Returns
string

### getUser


```php
public function getUser(): string
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

*<small>Implements Models\TupleKeyInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

