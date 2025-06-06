# BatchTupleResultInterface

Interface for batch tuple operation results. Defines the contract for tracking and analyzing the results of batch tuple operations, including success rates, responses, and error handling.

## Namespace

`OpenFGA\Models`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php)

## Implements

* [`ModelInterface`](ModelInterface.md)
* `JsonSerializable`

## Related Classes

* [BatchTupleResult](Models/BatchTupleResult.md) (implementation)

## Methods

### List Operations

#### getErrors

```php
public function getErrors(): array<Throwable>

```

Get all errors from failed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L32)

#### Returns

`array&lt;`Throwable`&gt;` — Errors from failed API calls

#### getFailedChunks

```php
public function getFailedChunks(): int

```

Get the number of chunks that failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L39)

#### Returns

`int` — Number of failed API requests

#### getFirstError

```php
public function getFirstError(): Throwable|null

```

Get the first error that occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L46)

#### Returns

`Throwable` &#124; `null` — The first error, or null if no errors occurred

#### getResponses

```php
public function getResponses(): array<mixed>

```

Get all successful responses from completed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L53)

#### Returns

`array&lt;`mixed`&gt;` — Responses from successful API calls

#### getSuccessRate

```php
public function getSuccessRate(): float

```

Calculate the success rate as a percentage.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L67)

#### Returns

`float` — Success rate from 0.0 to 1.0

#### getSuccessfulChunks

```php
public function getSuccessfulChunks(): int

```

Get the number of chunks that completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L60)

#### Returns

`int` — Number of successful API requests

#### getTotalChunks

```php
public function getTotalChunks(): int

```

Get the total number of chunks that were processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L74)

#### Returns

`int` — Number of API requests made

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of tuple operations that were requested.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L81)

#### Returns

`int` — Total operations across all chunks

### Utility

#### isCompleteFailure

```php
public function isCompleteFailure(): bool

```

Check if all chunks failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L88)

#### Returns

`bool` — True if no chunks succeeded

#### isCompleteSuccess

```php
public function isCompleteSuccess(): bool

```

Check if all chunks completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L95)

#### Returns

`bool` — True if no chunks failed

#### isPartialSuccess

```php
public function isPartialSuccess(): bool

```

Check if some chunks succeeded and some failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L102)

#### Returns

`bool` — True if there were both successes and failures

### Other

#### jsonSerialize

```php
public function jsonSerialize()

```

#### throwOnFailure

```php
public function throwOnFailure(): void

```

Throw an exception if any chunks failed. If there were failures, throws the first error that occurred. This is useful for treating partial failures as complete failures when strict error handling is required.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Models/BatchTupleResultInterface.php#L113)

#### Returns

`void`
