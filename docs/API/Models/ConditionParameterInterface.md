# ConditionParameterInterface


## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](Models/ModelInterface.md)
* JsonSerializable



## Methods
### getGenericTypes


```php
public function getGenericTypes(): ?OpenFGA\Models\Collections\ConditionParametersInterface
```

Get the generic type parameters for complex types like maps and lists. This provides the nested type information for complex parameter types. For example, a map parameter would have generic types defining the key and value types, while a list parameter would define the element type.


#### Returns
?[ConditionParametersInterface](Models/Collections/ConditionParametersInterface.md)

### getTypeName


```php
public function getTypeName(): TypeName
```

Get the primary type name of the parameter. This returns the fundamental type of the condition parameter, such as string, int, bool, list, map, etc. This type information is used during condition evaluation to ensure type safety.


#### Returns
TypeName
 The type name enum value for this parameter

### jsonSerialize


```php
public function jsonSerialize(): array
```



#### Returns
array

