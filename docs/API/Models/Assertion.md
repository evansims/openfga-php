# Assertion

Tests whether your authorization model behaves correctly for specific scenarios. Assertions are test cases that verify your authorization rules work as expected. Each assertion checks if a specific authorization question (like &quot;can user:anne read document:budget&quot;) returns the expected result (true for granted, false for denied). Use assertions to validate your authorization model during development and catch permission logic errors before they reach production. They&#039;re especially valuable when making changes to complex authorization rules.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Constants](#constants)
* [Methods](#methods)

* [List Operations](#list-operations)
    * [`getContext()`](#getcontext)
    * [`getContextualTuples()`](#getcontextualtuples)
    * [`getExpectation()`](#getexpectation)
    * [`getTupleKey()`](#gettuplekey)
* [Model Management](#model-management)
    * [`schema()`](#schema)
* [Other](#other)
    * [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php)

## Implements

* [`AssertionInterface`](AssertionInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [AssertionInterface](Models/AssertionInterface.md) (interface)

## Constants

| Name            | Value       | Description |
| --------------- | ----------- | ----------- |
| `OPENAPI_MODEL` | `Assertion` |             |

## Methods

### List Operations

#### getContext

```php
public function getContext(): ?array

```

Get the context data for evaluating ABAC conditions. Context provides additional information that can be used when evaluating attribute-based access control (ABAC) conditions. This might include user attributes, resource properties, or environmental factors like time of day.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php#L176)

#### Returns

`array` &#124; `null`

#### getContextualTuples

```php
public function getContextualTuples(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get the contextual tuples for this assertion. Contextual tuples provide additional relationship data that should be considered when evaluating the assertion. These are temporary relationships that exist only for the duration of the authorization check, useful for testing &quot;what-if&quot; scenarios.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php#L185)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null`

#### getExpectation

```php
public function getExpectation(): bool

```

Get the expected result for this assertion. The expectation defines whether the authorization check should return true (access granted) or false (access denied). This is what the assertion will be tested against.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php#L194)

#### Returns

`bool` — True if access should be granted, false if access should be denied

#### getTupleKey

```php
public function getTupleKey(): OpenFGA\Models\AssertionTupleKeyInterface

```

Get the tuple key that defines what to test. The tuple key specifies the exact authorization question to ask: &quot;Does user X have relation Y on object Z?&quot; This is the core of what the assertion is testing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php#L203)

#### Returns

[`AssertionTupleKeyInterface`](AssertionTupleKeyInterface.md) — The tuple key defining the authorization question

### Model Management

#### schema

*<small>Implements Models\AssertionInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/Assertion.php#L212)

#### Returns

`array`
