# ConditionParameter


## Namespace
`OpenFGA\Models`

## Implements
* [ConditionParameterInterface](Models/ConditionParameterInterface.md)
* JsonSerializable
* [ModelInterface](Models/ModelInterface.md)

## Constants
| Name | Value | Description |
|------|-------|-------------|
| `OPENAPI_MODEL` | `&#039;ConditionParamTypeRef&#039;` |  |


## Methods
### getGenericTypes


```php
public function getGenericTypes(): ?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)
```



#### Returns
`?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)`

### getTypeName


```php
public function getTypeName(): TypeName
```



#### Returns
`TypeName`

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
`array`

### schema

*<small>Implements Models\ConditionParameterInterface</small>*  

```php
public function schema(): [SchemaInterface](Schema/SchemaInterface.md)
```



#### Returns
`[SchemaInterface](Schema/SchemaInterface.md)`

