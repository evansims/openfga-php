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
public function getKey(): [TupleKeyInterface](Models/TupleKeyInterface.md)
```



#### Returns
`[TupleKeyInterface](Models/TupleKeyInterface.md)`

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`
 array&lt;&#039;condition&#039;|&#039;object&#039;|&#039;relation&#039;|&#039;user&#039;, array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list&lt;array{generic_types?: mixed, type_name: string}&gt;}|string&gt;, timestamp: string}

### schema

*<small>Implements Models\TupleInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

