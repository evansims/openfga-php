# TupleChange


## Namespace
`OpenFGA\Models`

## Implements
* [TupleChangeInterface](Models/TupleChangeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_TYPE` | `&#039;TupleChange&#039;` |  |


## Methods
### getOperation


```php
public function getOperation(): OpenFGA\Models\Enums\TupleOperation
```



#### Returns
TupleOperation

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```



#### Returns
DateTimeImmutable

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface
```



#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TupleChangeInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

