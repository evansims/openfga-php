# Assertion


## Namespace
`OpenFGA\Models`

## Implements
* `OpenFGA\Models\AssertionInterface`
* `JsonSerializable`
* `OpenFGA\Models\ModelInterface`

## Methods
### getContext

```php
public function getContext(): ?array
```



#### Returns
`?array` 

### getContextualTuples

```php
public function getContextualTuples(): ?TupleKeysInterface
```



#### Returns
`?TupleKeysInterface` 

### getExpectation

```php
public function getExpectation(): bool
```



#### Returns
`bool` 

### getTupleKey

```php
public function getTupleKey(): AssertionTupleKeyInterface
```



#### Returns
`AssertionTupleKeyInterface` 

### jsonSerialize

```php
public function jsonSerialize(): array
```



#### Returns
`array` 

