# BatchTupleOperation

Represents a batch tuple operation containing both writes and deletes. This model organizes tuple operations for batch processing, allowing you to specify both tuples to write and tuples to delete in a single operation. The batch processor will automatically chunk these operations to respect API limits while maintaining the grouping of related changes.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Constants](#constants)
* [Methods](#methods)

* [CRUD Operations](#crud-operations)
    * [`getDeletes()`](#getdeletes)
    * [`getWrites()`](#getwrites)
* [List Operations](#list-operations)
    * [`getTotalOperations()`](#gettotaloperations)
* [Model Management](#model-management)
    * [`schema()`](#schema)
* [Utility](#utility)
    * [`isEmpty()`](#isempty)
* [Other](#other)
    * [`chunk()`](#chunk)
    * [`jsonSerialize()`](#jsonserialize)
    * [`requiresChunking()`](#requireschunking)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php)

## Implements

* [`BatchTupleOperationInterface`](BatchTupleOperationInterface.md)
* `JsonSerializable`
* [`ModelInterface`](ModelInterface.md)

## Related Classes

* [BatchTupleOperationInterface](Models/BatchTupleOperationInterface.md) (interface)

## Constants

| Name                     | Value                 | Description                                       |
| ------------------------ | --------------------- | ------------------------------------------------- |
| `MAX_TUPLES_PER_REQUEST` | `100`                 | Maximum number of tuples allowed per API request. |
| `OPENAPI_MODEL`          | `BatchTupleOperation` |                                                   |

## Methods

### CRUD Operations

#### getDeletes

```php
public function getDeletes(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get the tuples to delete in this operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L132)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of tuples to delete, or null if none

#### getWrites

```php
public function getWrites(): ?OpenFGA\Models\Collections\TupleKeysInterface

```

Get the tuples to write in this operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L153)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of tuples to write, or null if none

### List Operations

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of operations (writes + deletes).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L141)

#### Returns

`int` — Total count of tuples to be processed

### Model Management

#### schema

*<small>Implements Models\BatchTupleOperationInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the JSON schema for this model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L25)

#### Returns

`SchemaInterface` — The schema definition

### Utility

#### isEmpty

```php
public function isEmpty(): bool

```

Check if this operation is empty (no writes or deletes).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L162)

#### Returns

`bool` — True if no operations are defined

### Other

#### chunk

```php
public function chunk(int $chunkSize = 100): array

```

Split this operation into smaller chunks that respect API limits. If the operation doesn&#039;t require chunking, returns an array containing only this operation. Otherwise, splits the writes and deletes across multiple operations to stay within the specified chunk size.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L71)

#### Parameters

| Name         | Type  | Description                                   |
| ------------ | ----- | --------------------------------------------- |
| `$chunkSize` | `int` | Maximum tuples per chunk (default: API limit) |

#### Returns

`array` — Array of operations, each within the chunk size

#### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L173)

#### Returns

`array&lt;`string`, `mixed`&gt;`

#### requiresChunking

```php
public function requiresChunking(int $chunkSize = 100): bool

```

Check if this operation requires chunking due to size limits.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperation.php#L185)

#### Parameters

| Name         | Type  | Description                                   |
| ------------ | ----- | --------------------------------------------- |
| `$chunkSize` | `int` | Maximum tuples per chunk (default: API limit) |

#### Returns

`bool` — True if the operation exceeds the specified chunk size
