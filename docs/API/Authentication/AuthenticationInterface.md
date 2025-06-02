# AuthenticationInterface

Interface for OpenFGA authentication strategies. This interface defines the contract for different authentication methods that can be used with the OpenFGA client. Authentication strategies handle the generation and management of authorization headers for API requests.

## Namespace
`OpenFGA\Authentication`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AuthenticationInterface.php)




## Methods
### getAuthenticationRequest


```php
public function getAuthenticationRequest(StreamFactoryInterface $streamFactory): RequestContext|null
```

Get an authentication request context if this strategy requires token acquisition. Returns a RequestContext for making an authentication request (such as OAuth token request) if the strategy needs to obtain tokens dynamically. Returns null for strategies that don&#039;t require authentication requests (like pre-shared tokens).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AuthenticationInterface.php#L31)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$streamFactory` | StreamFactoryInterface | Factory for creating request body streams |

#### Returns
RequestContext&#124;null
 The authentication request context, or null if not needed

### getAuthorizationHeader


```php
public function getAuthorizationHeader(): string|null
```

Get the authorization header value for API requests. Returns the authorization header value to be included in HTTP requests to the OpenFGA API. The format and content depend on the specific authentication strategy implementation. For strategies that need to perform authentication requests (like OAuth), this method may trigger an authentication flow using getAuthenticationRequest().

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AuthenticationInterface.php#L45)


#### Returns
string&#124;null
 The authorization header value, or null if no authentication is needed

### handleAuthenticationResponse


```php
public function handleAuthenticationResponse(ResponseInterface $response): void
```

Handle the authentication response and update internal state. This method is called by the Client after successfully sending an authentication request to update stored tokens or other authentication state. Implementations that don&#039;t require response handling can provide an empty implementation.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AuthenticationInterface.php#L56)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | ResponseInterface | The authentication response |

#### Returns
void

### requiresAuthentication


```php
public function requiresAuthentication(): bool
```

Check if authentication is required for this strategy.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AuthenticationInterface.php#L63)


#### Returns
bool
 True if this strategy provides authentication, false for no-auth strategies

