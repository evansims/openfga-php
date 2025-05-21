# TupleChange


## Namespace
`OpenFGA\Models`

## Implements
* [TupleChangeInterface](Models/TupleChangeInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Methods
### getOperation


```php
public function getOperation(): TupleOperation
```



#### Returns
`TupleOperation` 

### getTimestamp


```php
public function getTimestamp(): DateTimeImmutable
```



#### Returns
`DateTimeImmutable` 

### getTupleKey


```php
public function getTupleKey(): [TupleKeyInterface](Models/TupleKeyInterface.md)
```



#### Returns
`[TupleKeyInterface](Models/TupleKeyInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` &#039;TUPLE_OPERATION_DELETE&#039;|&#039;TUPLE_OPERATION_WRITE&#039;, timestamp: string, tuple_key: array&lt;&#039;condition&#039;|&#039;object&#039;|&#039;relation&#039;|&#039;user&#039;, array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list&lt;array{generic_types?: mixed, type_name: string}&gt;}|string&gt;}

### schema

*<small>Implements Models\TupleChangeInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)` 

