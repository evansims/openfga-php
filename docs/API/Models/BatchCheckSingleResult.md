# BatchCheckSingleResult

Represents the result of a single check within a batch check response. Each result contains whether the check was allowed and any error information if the check failed to complete successfully.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`getAllowed()`](#getallowed)
  - [`getError()`](#geterror)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)
  - [`toArray()`](#toarray)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResult.php)

## Implements

- [`BatchCheckSingleResultInterface`](BatchCheckSingleResultInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [BatchCheckSingleResultInterface](Models/BatchCheckSingleResultInterface.md) (interface)

## Constants

| Name            | Value                    | Description |
| --------------- | ------------------------ | ----------- |
| `OPENAPI_MODEL` | `BatchCheckSingleResult` |             |

## Methods

### getAllowed

```php
public function getAllowed(): ?bool

```

Get whether this check was allowed. Returns true if the user has the specified relationship with the object, false if they don&#039;t, or null if the check encountered an error.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResult.php#L91)

#### Returns

`bool` &#124; `null`

### getError

```php
public function getError(): ?object

```

Get any error that occurred during this check. Returns error information if the check failed to complete successfully, or null if the check completed without errors.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResult.php#L100)

#### Returns

`object` &#124; `null`

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResult.php#L111)

#### Returns

`array&lt;`string`, `mixed`&gt;`

### schema

*<small>Implements Models\BatchCheckSingleResultInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model

### toArray

```php
public function toArray(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchCheckSingleResult.php#L124)

#### Returns

`array&lt;`string`, `mixed`&gt;`
