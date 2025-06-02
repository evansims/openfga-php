# ConfigurationError

Configuration error types for the OpenFGA SDK. Defines specific configuration-related failures that can occur when setting up or using the SDK, typically related to missing required PSR HTTP components or invalid configuration parameters. Each case provides a factory method to create the corresponding ConfigurationException. Configuration errors usually occur during SDK initialization when required dependencies are missing or improperly configured. These errors indicate that the SDK cannot operate properly due to missing PSR-7/PSR-17/PSR-18 components or invalid configuration settings.

## Namespace

`OpenFGA\Exceptions`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ConfigurationError.php)

## Implements

* `UnitEnum`
* `BackedEnum`

## Constants

| Name                         | Value                                                                | Description |
| ---------------------------- | -------------------------------------------------------------------- | ----------- |
| `HttpClientMissing`          | `\OpenFGA\Exceptions\ConfigurationError::HttpClientMissing`          |             |
| `HttpRequestFactoryMissing`  | `\OpenFGA\Exceptions\ConfigurationError::HttpRequestFactoryMissing`  |             |
| `HttpResponseFactoryMissing` | `\OpenFGA\Exceptions\ConfigurationError::HttpResponseFactoryMissing` |             |
| `HttpStreamFactoryMissing`   | `\OpenFGA\Exceptions\ConfigurationError::HttpStreamFactoryMissing`   |             |

## Cases

| Name                         | Value                           | Description |
| ---------------------------- | ------------------------------- | ----------- |
| `HttpClientMissing`          | `http_client_missing`           |             |
| `HttpRequestFactoryMissing`  | `http_request_factory_missing`  |             |
| `HttpResponseFactoryMissing` | `http_response_factory_missing` |             |
| `HttpStreamFactoryMissing`   | `http_stream_factory_missing`   |             |

## Methods

### List Operations

#### getRequiredPsrInterface

```php
public function getRequiredPsrInterface(): string

```

Get the recommended PSR interface for this configuration error. Provides the specific PSR interface name that should be implemented to resolve this configuration error, useful for error messages and documentation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ConfigurationError.php#L82)

#### Returns

`string` — The PSR interface name

### Utility

#### isHttpComponentMissing

```php
public function isHttpComponentMissing(): true

```

Check if this configuration error is related to missing PSR HTTP components. Useful for providing specific error handling and setup guidance when PSR HTTP dependencies are not properly configured.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ConfigurationError.php#L100)

#### Returns

`true` — True if the error is related to missing HTTP components, false otherwise

### Other

#### exception

```php
public function exception(
    RequestInterface|null $request = NULL,
    ResponseInterface|null $response = NULL,
    array<string, mixed> $context = [],
    Throwable|null $prev = NULL,
): ConfigurationException

```

Create a new ConfigurationException for this error type. Factory method that creates a ConfigurationException instance with the current error type and provided context information. This provides a convenient way to generate typed exceptions with proper error categorization and rich debugging context for OpenFGA configuration failures. The exception will automatically capture the correct file and line location where this method was called (typically where `throw` occurs), ensuring debuggers show the actual throw location rather than this factory method.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ConfigurationError.php#L62)

#### Parameters

| Name        | Type                                                                | Description                                                                             |
| ----------- | ------------------------------------------------------------------- | --------------------------------------------------------------------------------------- |
| `$request`  | [`RequestInterface`](Requests/RequestInterface.md) &#124; `null`    | The PSR-7 HTTP request being processed when configuration error occurred, if applicable |
| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The PSR-7 HTTP response received, if applicable                                         |
| `$context`  | `array&lt;`string`, `mixed`&gt;`                                    |                                                                                         |
| `$prev`     | `Throwable` &#124; `null`                                           | The previous throwable used for exception chaining, if any                              |

#### Returns

[`ConfigurationException`](ConfigurationException.md) — The newly created ConfigurationException instance with comprehensive error context
