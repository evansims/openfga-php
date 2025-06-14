# AuthenticationServiceInterface

Service interface for managing authentication in OpenFGA operations. This service abstracts authentication concerns from the Client class, handling the complexities of token management, authentication flows, and authorization header generation. It provides a clean interface for different authentication strategies while encapsulating the details of token refresh and error handling. ## Core Functionality The service manages the complete authentication lifecycle: - Authorization header generation with automatic token refresh - Authentication request handling with proper error management - Support for multiple authentication strategies (OAuth2, pre-shared keys) - Integration with telemetry for authentication monitoring ## Usage Example ```php $authService = new AuthenticationService($authentication, $telemetryService); Get authorization header (with automatic refresh if needed) $authHeader = $authService-&gt;getAuthorizationHeader($streamFactory); Handle authentication requests $response = $authService-&gt;sendAuthenticationRequest($context, $requestManager); ```

## Table of Contents

- [Namespace](#namespace)
- [Source](#source)
- [Related Classes](#related-classes)
- [Methods](#methods)

- [List Operations](#list-operations)
  - [`getAuthorizationHeader()`](#getauthorizationheader)
- [Other](#other)
  - [`sendAuthenticationRequest()`](#sendauthenticationrequest)

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationServiceInterface.php)

## Related Classes

- [AuthenticationService](Services/AuthenticationService.md) (implementation)

## Methods

### List Operations

#### getAuthorizationHeader

```php
public function getAuthorizationHeader(
    StreamFactoryInterface $streamFactory,
    ?callable $requestSender = NULL,
): string|null

```

Get the authorization header for API requests. Retrieves the current authorization header, automatically handling token refresh if the current token is expired or missing. This method encapsulates the complexity of different authentication flows and provides a simple interface for obtaining valid authorization credentials.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationServiceInterface.php#L65)

#### Parameters

| Name             | Type                     | Description                                         |
| ---------------- | ------------------------ | --------------------------------------------------- |
| `$streamFactory` | `StreamFactoryInterface` | Stream factory for building authentication requests |
| `$requestSender` | `callable` &#124; `null` |                                                     |

#### Returns

`string` &#124; `null` — The authorization header value, or null if no authentication configured

### Other

#### sendAuthenticationRequest

```php
public function sendAuthenticationRequest(RequestContext $context, callable $requestSender): HttpResponseInterface

```

Send an authentication request using a pre-built RequestContext. Handles the complete lifecycle of authentication requests, including request building, sending, response handling, and telemetry tracking. This method provides a centralized point for all authentication-related HTTP operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/AuthenticationServiceInterface.php#L87)

#### Parameters

| Name             | Type             | Description                        |
| ---------------- | ---------------- | ---------------------------------- |
| `$context`       | `RequestContext` | The authentication request context |
| `$requestSender` | `callable`       |                                    |

#### Returns

`HttpResponseInterface` — The authentication response
