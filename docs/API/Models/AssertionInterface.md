# AssertionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getContext


```php
public function getContext(): null | array<string, mixed>
```



#### Returns
`null | array<string, mixed>`

### getContextualTuples


```php
public function getContextualTuples(): null | TupleKeysInterface<TupleKeyInterface>
```



#### Returns
`null | TupleKeysInterface<TupleKeyInterface>`

### getExpectation


```php
public function getExpectation(): bool
```



#### Returns
`bool`

### getTupleKey


```php
public function getTupleKey(): [AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)
```



#### Returns
`[AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

