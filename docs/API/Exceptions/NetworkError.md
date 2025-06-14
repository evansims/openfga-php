# NetworkError

Network error types for the OpenFGA SDK. Defines specific network and HTTP-related failures that can occur when communicating with the OpenFGA API, including HTTP status code errors, request failures, and timeouts. Each case provides a factory method to create the corresponding NetworkException. Network errors represent failures in HTTP communication with the OpenFGA service, ranging from client-side request errors to server-side failures. These errors often contain valuable debugging information in the HTTP response, including error messages and suggested remediation steps.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Constants](#constants)
- [Cases](#cases)
- [Methods](#methods)

- [Other](#other)
  - [`exception()`](#exception)

## Namespace

`OpenFGA\Exceptions`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/NetworkError.php)

## Implements

- `UnitEnum`
- `BackedEnum`

## Constants

| Name                | Value            | Description                                                                                                                                                                                            |
| ------------------- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `Conflict`          | `http_409`       | HTTP 409 Conflict - Resource conflicts with current state. Indicates that the request conflicts with the current state of the target resource, often due to concurrent modifications.                  |
| `Forbidden`         | `http_403`       | HTTP 403 Forbidden - Access denied to the requested resource. The server understood the request but refuses to authorize it, typically due to insufficient permissions.                                |
| `Invalid`           | `http_400`       | HTTP 400 Bad Request - Invalid request format or parameters. The server cannot process the request due to malformed syntax, invalid parameters, or missing required data.                              |
| `Request`           | `request_failed` | General request failure not related to HTTP status codes. Represents network-level failures such as DNS resolution errors, connection timeouts, or other transport-level issues.                       |
| `Server`            | `http_500`       | HTTP 500 Internal Server Error - Server-side processing failure. Indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.                          |
| `Timeout`           | `http_422`       | HTTP 422 Unprocessable Entity - Request timeout or processing limit exceeded. The server understands the request but cannot process it due to timeout constraints or processing limits being exceeded. |
| `Unauthenticated`   | `http_401`       | HTTP 401 Unauthorized - Authentication required or failed. The request requires valid authentication credentials that were not provided or are no longer valid.                                        |
| `UndefinedEndpoint` | `http_404`       | HTTP 404 Not Found - Requested endpoint or resource does not exist. The server cannot find the requested resource, which may indicate an invalid API endpoint or a resource that has been deleted.     |
| `Unexpected`        | `unexpected`     | Unexpected network error that doesn&#039;t fit other categories. Represents unusual network conditions or errors that are not covered by the standard HTTP status code categories.                     |

## Cases

| Name                | Value            | Description                                                                                                                                                                                            |
| ------------------- | ---------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------ |
| `Conflict`          | `http_409`       | HTTP 409 Conflict - Resource conflicts with current state. Indicates that the request conflicts with the current state of the target resource, often due to concurrent modifications.                  |
| `Forbidden`         | `http_403`       | HTTP 403 Forbidden - Access denied to the requested resource. The server understood the request but refuses to authorize it, typically due to insufficient permissions.                                |
| `Invalid`           | `http_400`       | HTTP 400 Bad Request - Invalid request format or parameters. The server cannot process the request due to malformed syntax, invalid parameters, or missing required data.                              |
| `Request`           | `request_failed` | General request failure not related to HTTP status codes. Represents network-level failures such as DNS resolution errors, connection timeouts, or other transport-level issues.                       |
| `Server`            | `http_500`       | HTTP 500 Internal Server Error - Server-side processing failure. Indicates that the server encountered an unexpected condition that prevented it from fulfilling the request.                          |
| `Timeout`           | `http_422`       | HTTP 422 Unprocessable Entity - Request timeout or processing limit exceeded. The server understands the request but cannot process it due to timeout constraints or processing limits being exceeded. |
| `Unauthenticated`   | `http_401`       | HTTP 401 Unauthorized - Authentication required or failed. The request requires valid authentication credentials that were not provided or are no longer valid.                                        |
| `UndefinedEndpoint` | `http_404`       | HTTP 404 Not Found - Requested endpoint or resource does not exist. The server cannot find the requested resource, which may indicate an invalid API endpoint or a resource that has been deleted.     |
| `Unexpected`        | `unexpected`     | Unexpected network error that doesn&#039;t fit other categories. Represents unusual network conditions or errors that are not covered by the standard HTTP status code categories.                     |

## Methods

#### exception

```php
public function exception(
    RequestInterface|null $request = NULL,
    ResponseInterface|null $response = NULL,
    array<string, mixed> $context = [],
    Throwable|null $prev = NULL,
): NetworkException

```

Create a new NetworkException for this error type. Factory method that creates a NetworkException instance with the current error type and provided context information. This provides a convenient way to generate typed exceptions with proper error categorization and rich debugging context for OpenFGA network failures. The exception will automatically capture the correct file and line location where this method was called (typically where `throw` occurs), ensuring debuggers show the actual throw location rather than this factory method.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/NetworkError.php#L126)

#### Parameters

| Name        | Type                                                                | Description                                                              |
| ----------- | ------------------------------------------------------------------- | ------------------------------------------------------------------------ |
| `$request`  | [`RequestInterface`](Requests/RequestInterface.md) &#124; `null`    | The PSR-7 HTTP request that triggered the network failure, if applicable |
| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The PSR-7 HTTP response containing error details, if applicable          |
| `$context`  | `array&lt;`string`, `mixed`&gt;`                                    |                                                                          |
| `$prev`     | `Throwable` &#124; `null`                                           | The previous throwable used for exception chaining, if any               |

#### Returns

[`NetworkException`](NetworkException.md) — The newly created NetworkException instance with comprehensive error context
