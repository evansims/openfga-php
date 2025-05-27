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
public function getGenericTypes(): ?OpenFGA\Models\Collections\ConditionParametersInterface
```



#### Returns
?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)

### getTypeName


```php
public function getTypeName(): OpenFGA\Models\Enums\TypeName
```



#### Returns
TypeName

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

### schema

*<small>Implements Models\ConditionParameterInterface</small>*  

```php
public function schema(): OpenFGA\Schema\SchemaInterface
```



#### Returns
[SchemaInterface](Schema/SchemaInterface.md)

