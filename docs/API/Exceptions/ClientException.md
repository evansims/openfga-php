# ClientException

General client exception for the OpenFGA SDK. Thrown for high-level client errors that can be categorized into different types such as authentication, configuration, network, serialization, or validation failures. Provides detailed context about the failure including the specific error category and any associated HTTP information.

## Namespace
`OpenFGA\Exceptions`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ClientException.php)

## Implements
* `Stringable`
* `Throwable`
* [`ClientThrowable`](ClientThrowable.md)

## Methods

### List Operations
#### getCode

```php
public function getCode()
```

#### getFile

```php
public function getFile(): string
```

#### Returns
`string`
#### getLine

```php
public function getLine(): int
```

#### Returns
`int`
#### getMessage

```php
public function getMessage(): string
```

#### Returns
`string`
#### getPrevious

```php
public function getPrevious(): ?Throwable
```

#### Returns
`Throwable` &#124; `null`
#### getTrace

```php
public function getTrace(): array
```

#### Returns
`array`
#### getTraceAsString

```php
public function getTraceAsString(): string
```

#### Returns
`string`
### Other
#### context

```php
public function context(): array<string, mixed>
```

Get additional context information about the exception. Provides access to contextual data that was available when the exception occurred, such as parameter values, configuration details, API response data, or other relevant debugging information. This context is essential for understanding the circumstances that led to the error and can be used for logging, debugging, and error reporting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ExceptionTrait.php#L32)

#### Returns
`array&lt;`string`, `mixed`&gt;` — Associative array of context data including parameter values, error details, and debugging information
#### kind

```php
public function kind(): OpenFGA\Exceptions\ClientError|OpenFGA\Exceptions\AuthenticationError|OpenFGA\Exceptions\ConfigurationError|OpenFGA\Exceptions\NetworkError|OpenFGA\Exceptions\SerializationError
```

Get the specific error category for this exception. Returns the error classification that indicates the general category of the problem (authentication, configuration, network, etc.), allowing for categorized error handling and reporting. This categorization helps applications implement appropriate retry logic, user messaging, and error recovery strategies based on the type of failure.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ExceptionTrait.php#L40)

#### Returns
[`ClientError`](ClientError.md) &#124; [`AuthenticationError`](AuthenticationError.md) &#124; [`ConfigurationError`](ConfigurationError.md) &#124; [`NetworkError`](NetworkError.md) &#124; [`SerializationError`](SerializationError.md) — The error category enum indicating the type of failure
#### previous

```php
public function previous(): ?Throwable
```

Get the previous exception that caused this one. Provides access to the exception chain for cases where this exception was triggered by another underlying exception. This maintains the full context of error propagation and is essential for root cause analysis when exceptions are wrapped or transformed during processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ExceptionTrait.php#L48)

#### Returns
`Throwable` &#124; `null` — The previous exception in the chain, or null if this is the root exception
#### request

```php
public function request(): ?Psr\Http\Message\RequestInterface
```

Get the HTTP request associated with this exception. Returns the PSR-7 HTTP request that was being processed when this exception occurred. This is particularly useful for debugging API call failures, allowing developers to inspect the request URL, headers, body, and method that led to the error condition.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ExceptionTrait.php#L56)

#### Returns
`Psr\Http\Message\RequestInterface` &#124; `null` — The PSR-7 HTTP request that triggered the exception, or null if not applicable
#### response

```php
public function response(): ?Psr\Http\Message\ResponseInterface
```

Get the HTTP response associated with this exception. Returns the PSR-7 HTTP response that was received when this exception occurred, providing access to status codes, headers, and response body for debugging. This is especially valuable for understanding API-level failures and can contain detailed error messages from the OpenFGA service.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ExceptionTrait.php#L64)

#### Returns
`Psr\Http\Message\ResponseInterface` &#124; `null` — The PSR-7 HTTP response received from the API, or null if no response was received
