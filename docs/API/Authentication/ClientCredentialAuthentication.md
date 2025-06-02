# ClientCredentialAuthentication

OAuth 2.0 Client Credentials authentication strategy for OpenFGA client. This authentication strategy implements the OAuth 2.0 Client Credentials flow for authenticating with the OpenFGA API. It automatically handles token acquisition, caching, and refresh when tokens expire. The strategy requires client credentials (client ID and secret) along with the OAuth issuer and audience parameters. It automatically requests new tokens when the current token expires.

## Namespace
`OpenFGA\Authentication`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php)

## Implements
* [AuthenticationInterface](AuthenticationInterface.md)




## Methods

                                                                                                
### List Operations
#### getAuthenticationRequest


```php
public function getAuthenticationRequest(Psr\Http\Message\StreamFactoryInterface $streamFactory): ?OpenFGA\Network\RequestContext
```

Get an authentication request context if this strategy requires token acquisition. Returns a RequestContext for making an authentication request (such as OAuth token request) if the strategy needs to obtain tokens dynamically. Returns null for strategies that don&#039;t require authentication requests (like pre-shared tokens).

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L86)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `response` | Psr\Http\Message\ResponseInterface |  |

#### Returns
?[RequestContext](Network/RequestContext.md)
 The authentication request context, or null if not needed

#### getAuthorizationHeader


```php
public function getAuthorizationHeader(): ?string
```

Get the authorization header value for API requests. Returns the authorization header value to be included in HTTP requests to the OpenFGA API. The format and content depend on the specific authentication strategy implementation. For strategies that need to perform authentication requests (like OAuth), this method may trigger an authentication flow using getAuthenticationRequest().

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L128)


#### Returns
?string
 The authorization header value, or null if no authentication is needed

#### getToken


```php
public function getToken(): AccessTokenInterface|null
```

Get the current access token if available. Returns the stored access token, which may be null if no authentication has been performed yet or if the token has been explicitly cleared. The returned token may be expired; use the token&#039;s isExpired() method to check validity.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L147)


#### Returns
[AccessTokenInterface](AccessTokenInterface.md) &#124; null
 The current access token, or null if not authenticated

### Other
#### clearToken


```php
public function clearToken(): void
```

Clear the current access token and force re-authentication. Removes the stored access token, forcing the authentication strategy to request a new token on the next API call. This is useful for handling authentication errors or forcing token refresh.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L75)


#### Returns
void

#### handleAuthenticationResponse


```php
public function handleAuthenticationResponse(ResponseInterface $response): void
```

Handle the authentication response and update the stored token. Processes the OAuth token response and creates a new access token from the response data. This method is automatically called by the Client after a successful authentication request. For JWT tokens, this method validates the issuer and audience claims against the OAuth configuration to ensure the token was issued by the expected authorization server and is intended for the correct audience.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L174)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$response` | [ResponseInterface](Responses/ResponseInterface.md) | The authentication response from the OAuth server |

#### Returns
void

#### requiresAuthentication


```php
public function requiresAuthentication(): bool
```

Check if authentication is required for this strategy.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/ClientCredentialAuthentication.php#L187)


#### Returns
bool
 True if this strategy provides authentication, false for no-auth strategies

