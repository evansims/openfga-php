# AssertionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getContext


```php
public function getContext(): ?array
```



#### Returns
`?array`
 mixed&gt;

### getContextualTuples


```php
public function getContextualTuples(): ?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)
```



#### Returns
`?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)`

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
```php
tuple_key: array&lt;string, mixed&gt;,
expectation: bool,
contextual_tuples?: array&lt;array-key, mixed&gt;,
context?: array&lt;array-key, mixed&gt;
}
```

