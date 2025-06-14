# BatchTupleResult

Represents the result of a batch tuple operation. This model tracks the results of processing a batch of tuple operations, including successful chunks, failed chunks, and overall statistics. It provides methods to analyze the success rate and retrieve details about any failures that occurred during processing.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Constants](#constants)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getErrors()`](#geterrors)
  - [`getFailedChunks()`](#getfailedchunks)
  - [`getFirstError()`](#getfirsterror)
  - [`getResponses()`](#getresponses)
  - [`getSuccessRate()`](#getsuccessrate)
  - [`getSuccessfulChunks()`](#getsuccessfulchunks)
  - [`getTotalChunks()`](#gettotalchunks)
  - [`getTotalOperations()`](#gettotaloperations)
- [Model Management](#model-management)
  - [`schema()`](#schema)
- [Utility](#utility)
  - [`isCompleteFailure()`](#iscompletefailure)
  - [`isCompleteSuccess()`](#iscompletesuccess)
  - [`isPartialSuccess()`](#ispartialsuccess)
- [Other](#other)
  - [`jsonSerialize()`](#jsonserialize)
  - [`throwOnFailure()`](#throwonfailure)

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php)

## Implements

- [`BatchTupleResultInterface`](BatchTupleResultInterface.md)
- `JsonSerializable`
- [`ModelInterface`](ModelInterface.md)

## Related Classes

- [BatchTupleResultInterface](Models/BatchTupleResultInterface.md) (interface)

## Constants

| Name            | Value              | Description |
| --------------- | ------------------ | ----------- |
| `OPENAPI_MODEL` | `BatchTupleResult` |             |

## Methods

### List Operations

#### getErrors

```php
public function getErrors(): array

```

Get all errors from failed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L78)

#### Returns

`array` — Errors from failed API calls

#### getFailedChunks

```php
public function getFailedChunks(): int

```

Get the number of chunks that failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L87)

#### Returns

`int` — Number of failed API requests

#### getFirstError

```php
public function getFirstError(): ?Throwable

```

Get the first error that occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L96)

#### Returns

`Throwable` &#124; `null` — The first error, or null if no errors occurred

#### getResponses

```php
public function getResponses(): array

```

Get all successful responses from completed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L105)

#### Returns

`array` — Responses from successful API calls

#### getSuccessRate

```php
public function getSuccessRate(): float

```

Calculate the success rate as a percentage.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L123)

#### Returns

`float` — Success rate from 0.0 to 1.0

#### getSuccessfulChunks

```php
public function getSuccessfulChunks(): int

```

Get the number of chunks that completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L114)

#### Returns

`int` — Number of successful API requests

#### getTotalChunks

```php
public function getTotalChunks(): int

```

Get the total number of chunks that were processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L136)

#### Returns

`int` — Number of API requests made

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of tuple operations that were requested.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L145)

#### Returns

`int` — Total operations across all chunks

### Model Management

#### schema

*<small>Implements Models\BatchTupleResultInterface</small>*

```php
public function schema(): SchemaInterface

```

Get the JSON schema for this model.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L25)

#### Returns

`SchemaInterface` — The schema definition

### Utility

#### isCompleteFailure

```php
public function isCompleteFailure(): bool

```

Check if all chunks failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L154)

#### Returns

`bool` — True if no chunks succeeded

#### isCompleteSuccess

```php
public function isCompleteSuccess(): bool

```

Check if all chunks completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L163)

#### Returns

`bool` — True if no chunks failed

#### isPartialSuccess

```php
public function isPartialSuccess(): bool

```

Check if some chunks succeeded and some failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L172)

#### Returns

`bool` — True if there were both successes and failures

### Other

#### jsonSerialize

```php
public function jsonSerialize(): array<string, mixed>

```

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L183)

#### Returns

`array&lt;`string`, `mixed`&gt;`

#### throwOnFailure

```php
public function throwOnFailure(): void

```

Throw an exception if any chunks failed. If there were failures, throws the first error that occurred. This is useful for treating partial failures as complete failures when strict error handling is required.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResult.php#L201)

#### Returns

`void`
