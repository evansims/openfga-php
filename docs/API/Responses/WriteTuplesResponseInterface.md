# WriteTuplesResponseInterface

Interface for tuple writing response objects. This interface defines the contract for responses returned when writing relationship tuples to an OpenFGA store. The response handles both transactional and non-transactional write modes, providing appropriate feedback for each operation type. In transactional mode, all changes succeed or fail together. In non-transactional mode, operations are processed independently with detailed success/failure tracking.

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php)

## Implements

* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [WriteTuplesResponse](Responses/WriteTuplesResponse.md) (implementation)
* [WriteTuplesRequestInterface](Requests/WriteTuplesRequestInterface.md) (request)

## Methods

### List Operations

#### getErrors

```php
public function getErrors(): array<Throwable>

```

Get all errors that occurred during processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L28)

#### Returns

`array&lt;`Throwable`&gt;` — Array of exceptions from failed operations

#### getFailedChunks

```php
public function getFailedChunks(): int

```

Get the number of failed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L35)

#### Returns

`int` — The number of failed chunks

#### getFirstError

```php
public function getFirstError(): Throwable|null

```

Get the first error that occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L42)

#### Returns

`Throwable` &#124; `null` — The first error, or null if no errors

#### getSuccessRate

```php
public function getSuccessRate(): float

```

Calculate the success rate of the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L56)

#### Returns

`float` — Success rate between 0.0 and 1.0

#### getSuccessfulChunks

```php
public function getSuccessfulChunks(): int

```

Get the number of successfully processed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L49)

#### Returns

`int` — The number of successful chunks

#### getTotalChunks

```php
public function getTotalChunks(): int

```

Get the total number of chunks processed (non-transactional mode).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L63)

#### Returns

`int` — The number of chunks, or 1 for transactional mode

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of tuple operations processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L70)

#### Returns

`int` — The total number of write and delete operations

### Utility

#### isCompleteFailure

```php
public function isCompleteFailure(): bool

```

Check if all operations failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L77)

#### Returns

`bool` — True if all operations failed

#### isCompleteSuccess

```php
public function isCompleteSuccess(): bool

```

Check if all operations completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L84)

#### Returns

`bool` — True if all operations succeeded

#### isPartialSuccess

```php
public function isPartialSuccess(): bool

```

Check if some operations succeeded and some failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L91)

#### Returns

`bool` — True if partial success (non-transactional mode only)

#### isTransactional

```php
public function isTransactional(): bool

```

Check if the operation was executed in transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L98)

#### Returns

`bool` — True if transactional, false if non-transactional

### Other

#### throwOnFailure

```php
public function throwOnFailure(): void

```

Throw an exception if any operations failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponseInterface.php#L105)

#### Returns

`void`
