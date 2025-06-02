# ConditionParameterInterface

Defines a parameter type for use in authorization conditions. ConditionParameter represents the type definition for parameters that can be passed to conditions during authorization evaluation. This includes simple types like strings and integers, as well as complex types like lists and maps with their own generic type parameters. Use this interface when defining conditions that accept typed parameters, ensuring type safety during authorization evaluation.

## Namespace
`OpenFGA\Models`

## Implements
* [ModelInterface](ModelInterface.md)
* JsonSerializable



## Methods
### getGenericTypes


```php
public function getGenericTypes(): ?OpenFGA\Models\Collections\ConditionParametersInterface
```

Get the generic type parameters for complex types like maps and lists. This provides the nested type information for complex parameter types. For example, a map parameter would have generic types defining the key and value types, while a list parameter would define the element type.


#### Returns
?OpenFGA\Models\Collections\ConditionParametersInterface

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

