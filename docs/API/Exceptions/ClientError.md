# ClientError

General client error types for the OpenFGA SDK. Defines high-level error categories that can occur when using the SDK, providing a way to classify different types of failures such as authentication, configuration, network, serialization, and validation errors. Each case provides a factory method to create the corresponding ClientException. These error categories help applications implement appropriate error handling strategies, retry logic, and user messaging based on the type of failure: - Authentication: Token-related issues, expired credentials, OAuth failures - Configuration: Missing PSR components, invalid SDK setup, configuration errors - Network: HTTP communication failures, timeouts, API connectivity issues - Serialization: JSON parsing errors, schema validation failures, data format issues - Validation: Parameter validation failures, invalid API usage, constraint violations

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Constants](#constants)
- [Cases](#cases)
- [Methods](#methods)

- [`exception()`](#exception)

</details>

## Namespace

`OpenFGA\Exceptions`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ClientError.php)

## Implements

- `UnitEnum`
- `BackedEnum`

## Constants

| Name             | Value            | Description                                                                                                                                                                              |
| ---------------- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Authentication` | `authentication` | Authentication-related failures when communicating with OpenFGA. Includes token expiration, invalid credentials, OAuth flow failures, or other authentication mechanism issues.          |
| `Configuration`  | `configuration`  | SDK configuration issues that prevent proper operation. Includes missing PSR HTTP components, invalid configuration parameters, or improper SDK setup that prevents API communication.   |
| `Network`        | `network`        | Network and HTTP communication failures with the OpenFGA API. Includes connectivity issues, timeouts, HTTP errors, or other network-level problems that prevent successful API requests. |
| `Serialization`  | `serialization`  | Data serialization and deserialization failures. Includes JSON parsing errors, schema validation failures, data type conversion issues, or other data format problems.                   |
| `Validation`     | `validation`     | Parameter validation and API usage errors. Includes invalid parameter values, constraint violations, improper API usage, or other validation failures before sending requests.           |

## Cases

| Name             | Value            | Description                                                                                                                                                                              |
| ---------------- | ---------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Authentication` | `authentication` | Authentication-related failures when communicating with OpenFGA. Includes token expiration, invalid credentials, OAuth flow failures, or other authentication mechanism issues.          |
| `Configuration`  | `configuration`  | SDK configuration issues that prevent proper operation. Includes missing PSR HTTP components, invalid configuration parameters, or improper SDK setup that prevents API communication.   |
| `Network`        | `network`        | Network and HTTP communication failures with the OpenFGA API. Includes connectivity issues, timeouts, HTTP errors, or other network-level problems that prevent successful API requests. |
| `Serialization`  | `serialization`  | Data serialization and deserialization failures. Includes JSON parsing errors, schema validation failures, data type conversion issues, or other data format problems.                   |
| `Validation`     | `validation`     | Parameter validation and API usage errors. Includes invalid parameter values, constraint violations, improper API usage, or other validation failures before sending requests.           |

## Methods

### exception

```php
public function exception(
    RequestInterface|null $request = NULL,
    ResponseInterface|null $response = NULL,
    array<string, mixed> $context = [],
    Throwable|null $prev = NULL,
): ClientException

```

Create a new ClientException for this error type. Factory method that creates a ClientException instance with the current error type and provided context information. This provides a convenient way to generate typed exceptions with proper error categorization and rich debugging context for OpenFGA API failures. The exception will automatically capture the correct file and line location where this method was called (typically where `throw` occurs), ensuring debuggers show the actual throw location rather than this factory method.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/ClientError.php#L98)

#### Parameters

| Name        | Type                                                                | Description                                                        |
| ----------- | ------------------------------------------------------------------- | ------------------------------------------------------------------ |
| `$request`  | [`RequestInterface`](Requests/RequestInterface.md) &#124; `null`    | The PSR-7 HTTP request that triggered the exception, if applicable |
| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The PSR-7 HTTP response received, if applicable                    |
| `$context`  | `array&lt;`string`, `mixed`&gt;`                                    |                                                                    |
| `$prev`     | `Throwable` &#124; `null`                                           | The previous throwable used for exception chaining, if any         |

#### Returns

[`ClientException`](ClientException.md) — The newly created ClientException instance with comprehensive error context
