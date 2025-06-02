# ConditionParameter

Represents a parameter type definition for ABAC conditions. ConditionParameter defines the type structure for parameters used in attribute-based access control conditions. It specifies the data type (string, int, list, map, etc.) and any generic type parameters for complex types like collections. Use this when defining the expected parameter types for conditions in your authorization model.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionParameter.php)

## Implements

* [`ConditionParameterInterface`](ConditionParameterInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [ConditionParameterInterface](Models/ConditionParameterInterface.md) (interface)
* [ConditionParameters](Models/Collections/ConditionParameters.md) (collection)

## Constants

| Name            | Value                     | Description |
| --------------- | ------------------------- | ----------- |
| `OPENAPI_MODEL` | `'ConditionParamTypeRef'` |             |

## Methods

### List Operations

#### getGenericTypes

```php
public function getGenericTypes(): ?OpenFGA\Models\Collections\ConditionParametersInterface

```

Get the generic type parameters for complex types like maps and lists. This provides the nested type information for complex parameter types. For example, a map parameter would have generic types defining the key and value types, while a list parameter would define the element type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionParameter.php#L58)

#### Returns

[`ConditionParametersInterface`](Models/Collections/ConditionParametersInterface.md) &#124; `null`

#### getTypeName

```php
public function getTypeName(): OpenFGA\Models\Enums\TypeName

```

Get the primary type name of the parameter. This returns the fundamental type of the condition parameter, such as string, int, bool, list, map, etc. This type information is used during condition evaluation to ensure type safety.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionParameter.php#L67)

#### Returns

[`TypeName`](Models/Enums/TypeName.md) — The type name enum value for this parameter

### Model Management

#### schema

*<small>Implements Models\ConditionParameterInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ConditionParameter.php#L76)

#### Returns

`array`
