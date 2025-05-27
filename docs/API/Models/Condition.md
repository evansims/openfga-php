# Condition


## Namespace
`OpenFGA\Models`

## Implements
* [ConditionInterface](Models/ConditionInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;Condition&#039;` |  |


## Methods
### getExpression


```php
public function getExpression(): string
```



#### Returns
string

### getMetadata


```php
public function getMetadata(): ?OpenFGA\Models\ConditionMetadataInterface
```



#### Returns
?[ConditionMetadataInterface](Models/ConditionMetadataInterface.md)

### getName


```php
public function getName(): string
```



#### Returns
string

### getParameters


```php
public function getParameters(): ?OpenFGA\Models\Collections\ConditionParametersInterface
```



#### Returns
?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\ConditionInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

