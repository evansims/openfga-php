# Tuple


## Namespace
`OpenFGA\Models`

## Implements
* [TupleInterface](Models/TupleInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Tuple&#039;` |  |


## Methods
### getKey


```php
public function getKey(): OpenFGA\Models\TupleKeyInterface
```



#### Returns
[TupleKeyInterface](Models/TupleKeyInterface.md)

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```



#### Returns
DateTimeImmutable

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\TupleInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

