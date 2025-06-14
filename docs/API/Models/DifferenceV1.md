# DifferenceV1

Represents a set difference operation between two usersets. In authorization models, you sometimes need to grant access to one group of users while explicitly excluding another group. DifferenceV1 calculates the difference between a base userset and a subtract userset, effectively giving you &quot;all users in base except those in subtract.&quot; For example, you might want to grant access to all employees except those in a specific department, or all document viewers except the document owner.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getBase()`](#getbase)
  - [`getSubtract()`](#getsubtract)
- [Model Management](#model-management)
  - [`schema()`](#schema)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1.php)

## Implements

- [`DifferenceV1Interface`](DifferenceV1Interface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [DifferenceV1Interface](Models/DifferenceV1Interface.md) (interface)

## Constants

| Name            | Value           | Description |
| --------------- | --------------- | ----------- |
| `OPENAPI_MODEL` | `v1.Difference` |             |

## Methods

### List Operations

#### getBase

```php
public function getBase(): OpenFGA\Models\UsersetInterface

```

Get the base userset from which users will be subtracted. This represents the initial set of users or relationships from which the subtract userset will be removed to compute the final difference.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1.php#L56)

#### Returns

[`UsersetInterface`](UsersetInterface.md) — The base userset for the difference operation

#### getSubtract

```php
public function getSubtract(): OpenFGA\Models\UsersetInterface

```

Get the userset of users to subtract from the base userset. This represents the set of users or relationships that should be removed from the base userset to compute the final result of the difference operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1.php#L65)

#### Returns

[`UsersetInterface`](UsersetInterface.md) — The userset to subtract from the base

### Model Management

#### schema

*<small>Implements Models\DifferenceV1Interface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/DifferenceV1.php#L74)

#### Returns

`array`
