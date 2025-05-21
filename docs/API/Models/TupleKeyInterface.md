# TupleKeyInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getCondition

```php
public function getCondition(): ?[ConditionInterface](Models/ConditionInterface.md)
```



#### Returns
`?[ConditionInterface](Models/ConditionInterface.md)` 

### getObject

```php
public function getObject(): ?string
```



#### Returns
`?string` 

### getRelation

```php
public function getRelation(): ?string
```



#### Returns
`?string` 

### getUser

```php
public function getUser(): ?string
```



#### Returns
`?string` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` array{expression: string, metadata?: array{module: string, source_info: array{file: string}}, name: string, parameters?: list&lt;array{generic_types?: mixed, type_name: string}&gt;}|string&gt;

