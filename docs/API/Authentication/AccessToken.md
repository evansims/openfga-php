# AccessToken

Immutable access token implementation for OpenFGA API authentication. This class represents an OAuth 2.0 access token with expiration tracking and scope management. Access tokens are typically obtained through OAuth flows and provide time-limited access to OpenFGA resources.

## Namespace
`OpenFGA\Authentication`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessToken.php)

## Implements
* [`AccessTokenInterface`](AccessTokenInterface.md)
* `Stringable`

## Related Classes
* [AccessTokenInterface](Authentication/AccessTokenInterface.md) (interface)

## Methods

### List Operations
#### getExpires

```php
public function getExpires(): int
```

Get the Unix timestamp when this access token expires. The expiration timestamp indicates when the token is no longer valid for API requests. Applications should check this value before making requests and refresh the token when necessary to avoid authentication failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessToken.php#L115)

#### Returns
`int` — Unix timestamp representing when the token expires
#### getScope

```php
public function getScope(): ?string
```

Get the scope that defines the permissions granted by this access token. The scope represents the extent of access granted to the token bearer. Different scopes may provide access to different OpenFGA operations or resources. A null scope typically indicates full access or that scope restrictions are not applicable for this token.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessToken.php#L124)

#### Returns
`string` &#124; `null` — The token scope defining granted permissions, or null if no scope is specified
#### getToken

```php
public function getToken(): string
```

Get the raw access token value. This method returns the actual token string that was issued by the authentication server. This is the same value returned by __toString() but provided as an explicit getter method for clarity.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessToken.php#L133)

#### Returns
`string` — The raw access token value
### Utility
#### isExpired

```php
public function isExpired(): bool
```

Check whether this access token has expired and needs to be refreshed. This method compares the token&#039;s expiration time against the current time to determine if the token is still valid. Expired tokens cannot be used for API requests as they result in authentication failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessToken.php#L142)

#### Returns
`bool` — True if the token has expired and should be refreshed, false if still valid
### Other
#### fromResponse

*<small>Implements Authentication\AccessTokenInterface</small>*

```php
public function fromResponse(
    ResponseInterface $response,
    string|null $expectedIssuer = NULL,
    string|null $expectedAudience = NULL,
): self
```

Create an access token instance from an OAuth server response. This factory method parses an HTTP response from an OAuth authorization server and extracts the access token information. The response should contain a JSON payload with the standard OAuth 2.0 token response fields including access_token, expires_in, and optionally scope. If the access token is a JWT and expectedIssuer/expectedAudience are provided, the JWT is validated to ensure the issuer and audience claims match the expected values from the OAuth client configuration.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php#L68)

#### Parameters
| Name                | Type                                                  | Description                                     |
| ------------------- | ----------------------------------------------------- | ----------------------------------------------- |
| `$response`         | [`ResponseInterface`](Responses/ResponseInterface.md) | The HTTP response from the OAuth token endpoint |
| `$expectedIssuer`   | `string` &#124; `null`                                | Optional expected issuer for JWT validation     |
| `$expectedAudience` | `string` &#124; `null`                                | Optional expected audience for JWT validation   |

#### Returns
`self` — A new access token instance created from the response data
