# AuthenticationService

Service implementation for managing authentication in OpenFGA operations. This service encapsulates all authentication-related logic, providing a clean abstraction over the underlying authentication strategies. It handles token management, authentication request flows, and integrates with telemetry for monitoring authentication performance and failures. The service supports multiple authentication strategies through the AuthenticationInterface, automatically handling token refresh and error recovery patterns. It provides consistent error handling and telemetry integration across all authentication operations.

<details>
<summary><strong>Table of Contents</strong></summary>

- [Namespace](#namespace)
- [Source](#source)
- [Implements](#implements)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [`getAuthorizationHeader()`](#getauthorizationheader)
  - [`sendAuthenticationRequest()`](#sendauthenticationrequest)

</details>

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationService.php)

## Implements

- [`AuthenticationServiceInterface`](AuthenticationServiceInterface.md)

## Related Classes

- [AuthenticationServiceInterface](Services/AuthenticationServiceInterface.md) (interface)

## Methods

### getAuthorizationHeader

```php
public function getAuthorizationHeader(
    Psr\Http\Message\StreamFactoryInterface $streamFactory,
    ?callable $requestSender = NULL,
): ?string

```

Get the authorization header for API requests. Retrieves the current authorization header, automatically handling token refresh if the current token is expired or missing. This method encapsulates the complexity of different authentication flows and provides a simple interface for obtaining valid authorization credentials.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationService.php#L48)

#### Parameters

| Name             | Type                     | Description                                         |
| ---------------- | ------------------------ | --------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | Stream factory for building authentication requests |
| `$requestSender` | `callable` &#124; `null` |                                                     |

#### Returns

`string` &#124; `null` — The authorization header value, or null if no authentication configured

### sendAuthenticationRequest

```php
public function sendAuthenticationRequest(
    OpenFGA\Network\RequestContext $context,
    callable $requestSender,
): Psr\Http\Message\ResponseInterface

```

Send an authentication request using a pre-built RequestContext. Handles the complete lifecycle of authentication requests, including request building, sending, response handling, and telemetry tracking. This method provides a centralized point for all authentication-related HTTP operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationService.php#L137)

#### Parameters

| Name             | Type                                          | Description                        |
| ---------------- | --------------------------------------------- | ---------------------------------- |
| `$context`       | [`RequestContext`](Network/RequestContext.md) | The authentication request context |
| `$requestSender` | `callable`                                    |                                    |

#### Returns

`Psr\Http\Message\ResponseInterface` — The authentication response
