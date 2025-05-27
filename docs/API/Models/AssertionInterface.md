# AssertionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getContext


```php
public function getContext(): null|array<string, mixed>
```



#### Returns
null | array&lt;string, mixed&gt;

### getContextualTuples


```php
public function getContextualTuples(): null|TupleKeysInterface<TupleKeyInterface>
```



#### Returns
null | TupleKeysInterface&lt;[TupleKeyInterface](Models/TupleKeyInterface.md)&gt;

### getExpectation


```php
public function getExpectation(): bool
```



#### Returns
bool

### getTupleKey


```php
public function getTupleKey(): OpenFGA\Models\AssertionTupleKeyInterface
```



#### Returns
[AssertionTupleKeyInterface](Models/AssertionTupleKeyInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

