# WriteTuplesResponse

Response for tuple writing operations supporting both transactional and non-transactional modes. This response handles results from both transactional writes (all-or-nothing) and non-transactional writes (independent operations with detailed tracking).

## Namespace

`OpenFGA\Responses`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php)

## Implements

* [`WriteTuplesResponseInterface`](WriteTuplesResponseInterface.md)
* [`ResponseInterface`](ResponseInterface.md)

## Related Classes

* [WriteTuplesResponseInterface](Responses/WriteTuplesResponseInterface.md) (interface)
* [WriteTuplesRequest](Requests/WriteTuplesRequest.md) (request)

## Methods

### List Operations

#### getErrors

```php
public function getErrors(): array

```

Get all errors that occurred during processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L80)

#### Returns

`array` — Array of exceptions from failed operations

#### getFailedChunks

```php
public function getFailedChunks(): int

```

Get the number of failed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L89)

#### Returns

`int` — The number of failed chunks

#### getFirstError

```php
public function getFirstError(): ?Throwable

```

Get the first error that occurred.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L98)

#### Returns

`Throwable` &#124; `null` — The first error, or null if no errors

#### getSuccessRate

```php
public function getSuccessRate(): float

```

Calculate the success rate of the operation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L116)

#### Returns

`float` — Success rate between 0.0 and 1.0

#### getSuccessfulChunks

```php
public function getSuccessfulChunks(): int

```

Get the number of successfully processed chunks.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L107)

#### Returns

`int` — The number of successful chunks

#### getTotalChunks

```php
public function getTotalChunks(): int

```

Get the total number of chunks processed (non-transactional mode).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L129)

#### Returns

`int` — The number of chunks, or 1 for transactional mode

#### getTotalOperations

```php
public function getTotalOperations(): int

```

Get the total number of tuple operations processed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L138)

#### Returns

`int` — The total number of write and delete operations

### Utility

#### isCompleteFailure

```php
public function isCompleteFailure(): bool

```

Check if all operations failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L147)

#### Returns

`bool` — True if all operations failed

#### isCompleteSuccess

```php
public function isCompleteSuccess(): bool

```

Check if all operations completed successfully.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L156)

#### Returns

`bool` — True if all operations succeeded

#### isPartialSuccess

```php
public function isPartialSuccess(): bool

```

Check if some operations succeeded and some failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L165)

#### Returns

`bool` — True if partial success (non-transactional mode only)

#### isTransactional

```php
public function isTransactional(): bool

```

Check if the operation was executed in transactional mode.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L174)

#### Returns

`bool` — True if transactional, false if non-transactional

### Other

#### fromResponse

*<small>Implements Responses\WriteTuplesResponseInterface</small>*

```php
public function fromResponse(
    HttpResponseInterface $response,
    HttpRequestInterface $request,
    SchemaValidatorInterface $validator,
): static

```

Create a response instance from an HTTP response. This method transforms a raw HTTP response from the OpenFGA API into a structured response object, validating and parsing the response data according to the expected schema. It handles both successful responses by parsing and validating the data, and error responses by throwing appropriate exceptions.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/ResponseInterface.php#L44)

#### Parameters

| Name         | Type                       | Description                                               |
| ------------ | -------------------------- | --------------------------------------------------------- |
| `$response`  | `HttpResponseInterface`    | The raw HTTP response from the OpenFGA API                |
| `$request`   | `HttpRequestInterface`     | The original HTTP request that generated this response    |
| `$validator` | `SchemaValidatorInterface` | Schema validator for parsing and validating response data |

#### Returns

`static` — The parsed and validated response instance containing the API response data

#### throwOnFailure

```php
public function throwOnFailure(): void

```

Throw an exception if any operations failed.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Responses/WriteTuplesResponse.php#L183)

#### Returns

`void`
