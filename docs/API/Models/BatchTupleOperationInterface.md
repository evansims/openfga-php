# BatchTupleOperationInterface

Interface for batch tuple operations. Defines the contract for organizing tuple writes and deletes into batches that can be processed efficiently while respecting API limitations.

## Table of Contents

* [Namespace](#namespace)
* [Source](#source)
* [Implements](#implements)
* [Related Classes](#related-classes)
* [Methods](#methods)

* [CRUD Operations](#crud-operations)
    * [`getDeletes()`](#getdeletes)
    * [`getWrites()`](#getwrites)
* [List Operations](#list-operations)
    * [`getTotalOperations()`](#gettotaloperations)
* [Utility](#utility)
    * [`isEmpty()`](#isempty)
* [Other](#other)
    * [`chunk()`](#chunk)
    * [`jsonSerialize()`](#jsonserialize)
    * [`requiresChunking()`](#requireschunking)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes

* [BatchTupleOperation](Models/BatchTupleOperation.md) (implementation)

## Methods

### CRUD Operations

#### getDeletes

```php
public function getDeletes(): TupleKeysInterface|null

```

Get the tuples to delete in this operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L44)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of tuples to delete, or null if none

#### getWrites

```php
public function getWrites(): TupleKeysInterface|null

```

Get the tuples to write in this operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L58)

#### Returns

[`TupleKeysInterface`](Models/Collections/TupleKeysInterface.md) &#124; `null` — Collection of tuples to write, or null if none

### List Operations

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of operations (writes + deletes).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L51)

#### Returns

`int` — Total count of tuples to be processed

### Utility

#### isEmpty

```php
public function isEmpty(): bool

```

Check if this operation is empty (no writes or deletes).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L65)

#### Returns

`bool` — True if no operations are defined

### Other

#### chunk

```php
public function chunk(int $chunkSize = 100): array<BatchTupleOperationInterface>

```

Split this operation into smaller chunks that respect API limits. If the operation doesn&#039;t require chunking, returns an array containing only this operation. Otherwise, splits the writes and deletes across multiple operations to stay within the specified chunk size.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L37)

#### Parameters

| Name         | Type  | Description                                   |
| ------------ | ----- | --------------------------------------------- |
| `$chunkSize` | `int` | Maximum tuples per chunk (default: API limit) |

#### Returns

`array&lt;[`BatchTupleOperationInterface`](BatchTupleOperationInterface.md)&gt;` — Array of operations, each within the chunk size

#### jsonSerialize

```php
public function jsonSerialize()

```

#### requiresChunking

```php
public function requiresChunking(int $chunkSize = 100): bool

```

Check if this operation requires chunking due to size limits.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleOperationInterface.php#L73)

#### Parameters

| Name         | Type  | Description                                   |
| ------------ | ----- | --------------------------------------------- |
| `$chunkSize` | `int` | Maximum tuples per chunk (default: API limit) |

#### Returns

`bool` — True if the operation exceeds the specified chunk size
