# TokenAuthentication

Token-based authentication strategy for OpenFGA client. This authentication strategy uses a pre-shared token (such as a Bearer token or API key) for authentication with the OpenFGA API. The token is provided during construction and used as-is for all requests. This strategy is suitable for scenarios where you have a long-lived token or when implementing custom token refresh logic externally. The strategy accepts either a string token or an AccessTokenInterface instance for maximum flexibility.

## Namespace

`OpenFGA\Authentication`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php)

## Implements

* [`AuthenticationInterface`](AuthenticationInterface.md)

## Methods

### List Operations

#### getAuthenticationRequest

```php
public function getAuthenticationRequest(
    Psr\Http\Message\StreamFactoryInterface $streamFactory,
): ?OpenFGA\Network\RequestContext

```

Get an authentication request context if this strategy requires token acquisition. Returns a RequestContext for making an authentication request (such as OAuth token request) if the strategy needs to obtain tokens dynamically. Returns null for strategies that don&#039;t require authentication requests (like pre-shared tokens).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php#L47)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$streamFactory` | `StreamFactoryInterface` | Factory for creating request body streams |

#### Returns

[`RequestContext`](Network/RequestContext.md) &#124; `null` — The authentication request context, or null if not needed

#### getAuthorizationHeader

```php
public function getAuthorizationHeader(): ?string

```

Get the authorization header value for API requests. Returns the authorization header value to be included in HTTP requests to the OpenFGA API. The format and content depend on the specific authentication strategy implementation. For strategies that need to perform authentication requests (like OAuth), this method may trigger an authentication flow using getAuthenticationRequest().

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php#L56)

#### Returns

`string` &#124; `null` — The authorization header value, or null if no authentication is needed

#### getToken

```php
public function getToken(): AccessTokenInterface|string

```

Get the current authentication token. Returns the token that was provided during construction. This can be either a string token or an AccessTokenInterface instance depending on what was originally provided to the constructor.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php#L78)

#### Returns

[`AccessTokenInterface`](AccessTokenInterface.md) &#124; `string` — The authentication token used by this strategy

### Other

#### handleAuthenticationResponse

```php
public function handleAuthenticationResponse(Psr\Http\Message\ResponseInterface $response): void

```

Handle the authentication response and update internal state. This method is called by the Client after successfully sending an authentication request to update stored tokens or other authentication state. Implementations that don&#039;t require response handling can provide an empty implementation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php#L87)

#### Parameters

| Name | Type | Description |

|------|------|-------------|

| `$response` | `Psr\Http\Message\ResponseInterface` | The authentication response |

#### Returns

`void`

#### requiresAuthentication

```php
public function requiresAuthentication(): bool

```

Check if authentication is required for this strategy.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/TokenAuthentication.php#L95)

#### Returns

`bool` — True if this strategy provides authentication, false for no-auth strategies
