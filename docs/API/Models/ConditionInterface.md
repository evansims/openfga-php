# ConditionInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable

## Methods
### getExpression


```php
public function getExpression(): string
```



#### Returns
`string` 

### getMetadata


```php
public function getMetadata(): ?[ConditionMetadataInterface](Models/ConditionMetadataInterface.md)
```



#### Returns
`?[ConditionMetadataInterface](Models/ConditionMetadataInterface.md)` 

### getName


```php
public function getName(): string
```



#### Returns
`string` 

### getParameters


```php
public function getParameters(): ?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)
```



#### Returns
`?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)` 

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array` string, expression: string, parameters?: list&lt;array{type_name: string, generic_types?: mixed}&gt;, metadata?: array{module: string, source_info: array{file: string}}}

