# Assertion


## Namespace
`OpenFGA\Models`

## Implements
* [AssertionInterface](Models/AssertionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Assertion&#039;` |  |


## Methods
### getContext


```php
public function getContext(): ?array
```



#### Returns
?array

### getContextualTuples


```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface
```



#### Returns
?[TupleKeysInterface](Models/Collections/TupleKeysInterface.md)

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

### schema

*<small>Implements Models\AssertionInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

