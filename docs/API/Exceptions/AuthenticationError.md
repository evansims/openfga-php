# AuthenticationError

Authentication error types for the OpenFGA SDK. Defines specific authentication failure scenarios that can occur when interacting with the OpenFGA API, such as expired or invalid tokens. Each case provides a factory method to create the corresponding AuthenticationException with appropriate context. Authentication errors typically occur during the OAuth 2.0 flow or when using access tokens with OpenFGA API requests. These errors indicate that the provided credentials are no longer valid or were never valid, requiring token refresh or re-authentication.

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Constants](#constants)
- [Cases](#cases)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getUserMessage()`](#getusermessage)
- [Utility](#utility)
  - [`isTokenRefreshable()`](#istokenrefreshable)
- [Other](#other)
  - [`exception()`](#exception)

## Namespace

`OpenFGA\Exceptions`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/AuthenticationError.php)

## Implements

- `UnitEnum`
- `BackedEnum`

## Constants

| Name           | Value           | Description                                                                                                                                                                     |
| -------------- | --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `TokenExpired` | `token_expired` | Access token has expired and needs to be refreshed. Occurs when an access token&#039;s expiration time has passed, requiring a new token to be obtained through the OAuth flow. |
| `TokenInvalid` | `token_invalid` | Access token is invalid or malformed. Occurs when the provided token is not recognized by the authorization server or has an invalid format.                                    |

## Cases

| Name           | Value           | Description                                                                                                                                                                     |
| -------------- | --------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `TokenExpired` | `token_expired` | Access token has expired and needs to be refreshed. Occurs when an access token&#039;s expiration time has passed, requiring a new token to be obtained through the OAuth flow. |
| `TokenInvalid` | `token_invalid` | Access token is invalid or malformed. Occurs when the provided token is not recognized by the authorization server or has an invalid format.                                    |

## Methods

### List Operations

#### getUserMessage

```php
public function getUserMessage(string|null $locale = NULL): string

```

Get a user-friendly error message for this authentication error. Provides appropriate messaging for different authentication failures that can be displayed to end users or used in error logs. Messages are localized using the translation system.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/AuthenticationError.php#L93)

#### Parameters

| Name      | Type                   | Description                                      |
| --------- | ---------------------- | ------------------------------------------------ |
| `$locale` | `string` &#124; `null` | Optional locale override for message translation |

#### Returns

`string` — A descriptive, localized error message

### Utility

#### isTokenRefreshable

```php
public function isTokenRefreshable(): bool

```

Check if this authentication error indicates the token should be refreshed. Useful for implementing automatic token refresh logic in OAuth flows.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/AuthenticationError.php#L110)

#### Returns

`bool` — True if token refresh should be attempted, false otherwise

### Other

#### exception

```php
public function exception(
    RequestInterface|null $request = NULL,
    ResponseInterface|null $response = NULL,
    array<string, mixed> $context = [],
    Throwable|null $prev = NULL,
): AuthenticationException

```

Create a new AuthenticationException for this error type. Factory method that creates an AuthenticationException instance with the current error type and provided context information. This provides a convenient way to generate typed exceptions with proper error categorization and rich debugging context for OpenFGA authentication failures. The exception will automatically capture the correct file and line location where this method was called (typically where `throw` occurs), ensuring debuggers show the actual throw location rather than this factory method.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/AuthenticationError.php#L71)

#### Parameters

| Name        | Type                                                                | Description                                                                     |
| ----------- | ------------------------------------------------------------------- | ------------------------------------------------------------------------------- |
| `$request`  | [`RequestInterface`](Requests/RequestInterface.md) &#124; `null`    | The PSR-7 HTTP request that triggered the authentication failure, if applicable |
| `$response` | [`ResponseInterface`](Responses/ResponseInterface.md) &#124; `null` | The PSR-7 HTTP response containing authentication error details, if applicable  |
| `$context`  | `array&lt;`string`, `mixed`&gt;`                                    |                                                                                 |
| `$prev`     | `Throwable` &#124; `null`                                           | The previous throwable used for exception chaining, if any                      |

#### Returns

[`AuthenticationException`](AuthenticationException.md) — The newly created AuthenticationException instance with comprehensive error context
