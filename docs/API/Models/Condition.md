# Condition

Represents an ABAC (Attribute-Based Access Control) condition in your authorization model. A Condition defines a logical expression that must evaluate to true for authorization to be granted. It includes the expression code, parameter definitions, and optional metadata. Conditions enable context-aware authorization decisions based on attributes of users, resources, and environment. Use this when implementing fine-grained access control that depends on runtime attributes and contextual information.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [`getContext()`](#getcontext)
  - [`getExpression()`](#getexpression)
  - [`getMetadata()`](#getmetadata)
  - [`getName()`](#getname)
  - [`getParameters()`](#getparameters)
  - [`jsonSerialize()`](#jsonserialize)
  - [`schema()`](#schema)

</details>

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php)

## Implements

- [`ConditionInterface`](ConditionInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [ConditionInterface](Models/ConditionInterface.md) (interface)
- [Conditions](Models/Collections/Conditions.md) (collection)

## Constants

| Name            | Value       | Description |
| --------------- | ----------- | ----------- |
| `OPENAPI_MODEL` | `Condition` |             |

## Methods

### getContext

```php
public function getContext(): ?array

```

Get the context for the condition. This provides additional runtime data that can be used by the condition&#039;s expression for dynamic evaluation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L66)

#### Returns

`array` &#124; `null` — the context data, or null if not provided

### getExpression

```php
public function getExpression(): string

```

Get the condition expression. This returns the logical expression that defines when this condition evaluates to true. The expression can reference parameters and context data to enable dynamic authorization decisions based on runtime information.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L75)

#### Returns

`string` — The condition expression defining the evaluation logic

### getMetadata

```php
public function getMetadata(): ?OpenFGA\Models\ConditionMetadataInterface

```

Get metadata about the condition definition. This provides additional information about where the condition was defined and how it should be processed, which is useful for tooling and debugging.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L84)

#### Returns

[`ConditionMetadataInterface`](ConditionMetadataInterface.md) &#124; `null` — The condition metadata, or null if not provided

### getName

```php
public function getName(): string

```

Get the name of the condition. This is a unique identifier for the condition within the authorization model, allowing it to be referenced from type definitions and other parts of the model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L93)

#### Returns

`string` — The unique name identifying this condition

### getParameters

```php
public function getParameters(): ?OpenFGA\Models\Collections\ConditionParametersInterface

```

Get the parameters available to the condition expression. These parameters define the typed inputs that can be used within the condition expression, enabling dynamic evaluation based on contextual data provided during authorization checks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L102)

#### Returns

[`ConditionParametersInterface`](Models/Collections/ConditionParametersInterface.md) &#124; `null` — The condition parameters, or null if the condition uses no parameters

### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

Serialize the condition for JSON encoding. This method prepares the condition data for API requests or storage, ensuring all components are properly formatted according to the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Condition.php#L113)

#### Returns

`array&lt;`string`, `mixed`&gt;` — The serialized condition data

### schema

*<small>Implements Models\ConditionInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the schema definition for this model. This method returns the schema that defines the structure, validation rules, and serialization behavior for this model class. The schema is used for data validation, transformation, and ensuring consistency across API operations with the OpenFGA service. Each model&#039;s schema defines: - Required and optional properties - Data types and format constraints - Nested object relationships - Validation rules and business logic constraints The schema system enables the SDK to automatically validate incoming data, transform between different representations, and ensure compliance with the OpenFGA API specification.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/ModelInterface.php#L52)

#### Returns

`SchemaInterface` — The schema definition containing validation rules and property specifications for this model
