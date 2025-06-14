# TupleChange

Represents a change to a relationship tuple in your authorization store. When you modify relationships in OpenFGA (adding or removing tuples), each change is tracked as a TupleChange. This allows you to see the history of authorization changes, audit permissions over time, and understand when relationships were established or removed. Use this when you need to track or review the history of relationship changes in your application, such as for compliance auditing or debugging permission issues.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getOperation()`](#getoperation)
  - [`getTimestamp()`](#gettimestamp)
  - [`getTupleKey()`](#gettuplekey)
- [Model Management](#model-management)
  - [`schema()`](#schema)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleChange.php)

## Implements

- [`TupleChangeInterface`](TupleChangeInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [TupleChangeInterface](Models/TupleChangeInterface.md) (interface)
- [TupleChanges](Models/Collections/TupleChanges.md) (collection)

## Constants

| Name            | Value         | Description |
| --------------- | ------------- | ----------- |
| `OPENAPI_MODEL` | `TupleChange` |             |

## Methods

### List Operations

#### getOperation

```php
public function getOperation(): OpenFGA\Models\Enums\TupleOperation

```

Get the type of operation performed on the tuple. Operations indicate whether the tuple was written (created) or deleted from the authorization store. This information is crucial for understanding the nature of the change.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleChange.php#L80)

#### Returns

[`TupleOperation`](Models/Enums/TupleOperation.md) — The operation type (write or delete)

#### getTimestamp

```php
public function getTimestamp(): DateTimeImmutable

```

Get the timestamp when this tuple change occurred. Timestamps help track the chronological order of changes and provide audit trail capabilities. They are essential for understanding the sequence of relationship modifications.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleChange.php#L89)

#### Returns

`DateTimeImmutable` — The change timestamp

#### getTupleKey

```php
public function getTupleKey(): OpenFGA\Models\TupleKeyInterface

```

Get the tuple key that was affected by this change. The tuple key identifies which specific relationship was created or deleted, containing the user, relation, object, and optional condition information.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleChange.php#L98)

#### Returns

[`TupleKeyInterface`](TupleKeyInterface.md) — The tuple key that was modified

### Model Management

#### schema

*<small>Implements Models\TupleChangeInterface</small>*

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/TupleChange.php#L107)

#### Returns

`array`
