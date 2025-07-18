# SourceInfo

Represents source file information for debugging and development tools. SourceInfo provides metadata about where elements of your authorization model were originally defined, including file paths. This information is valuable for development tools, error reporting, and debugging authorization model issues. Use this when you need to trace authorization model elements back to their source definitions for debugging or tooling purposes.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`getFile()`](#getfile)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfo.php)

## Implements

- [`SourceInfoInterface`](SourceInfoInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [SourceInfoInterface](Models/SourceInfoInterface.md) (interface)

## Constants

| Name            | Value        | Description |
| --------------- | ------------ | ----------- |
| `OPENAPI_MODEL` | `SourceInfo` |             |

## Methods

### getFile

```php
public function getFile(): string

```

Get the source file path where the model element was defined. This provides debugging and tooling information about the original source file location for the model element. This is particularly useful for development tools, error reporting, and tracing model definitions back to their source.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfo.php#L65)

#### Returns

`string` — The source file path where the element was defined

### jsonSerialize

```php
public function jsonSerialize(): array

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/SourceInfo.php#L74)

#### Returns

`array`

### schema

*<small>Implements Models\SourceInfoInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model
