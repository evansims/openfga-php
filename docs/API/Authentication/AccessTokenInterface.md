# AccessTokenInterface

Represents an access token for OpenFGA API authentication. Access tokens are credentials used to authenticate requests to the OpenFGA API. They are typically obtained through OAuth 2.0 flows (such as client credentials) and have a limited lifespan defined by their expiration time. Access tokens provide secure, time-limited access to OpenFGA resources without requiring the transmission of long-lived credentials with each request. This interface defines the contract for managing these tokens, including: - Token value retrieval for Authorization headers - Expiration checking to determine when token refresh is needed - Scope validation for permission boundaries - Token parsing from OAuth server responses

## Namespace

`OpenFGA\Authentication`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php)

## Implements

* `Stringable`

## Related Classes

* [AccessToken](Authentication/AccessToken.md) (implementation)

## Methods

### List Operations

#### getExpires

```php
public function getExpires(): int

```

Get the Unix timestamp when this access token expires. The expiration timestamp indicates when the token is no longer valid for API requests. Applications should check this value before making requests and refresh the token when necessary to avoid authentication failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php#L79)

#### Returns

`int` — Unix timestamp representing when the token expires

#### getScope

```php
public function getScope(): string|null

```

Get the scope that defines the permissions granted by this access token. The scope represents the extent of access granted to the token bearer. Different scopes may provide access to different OpenFGA operations or resources. A null scope typically indicates full access or that scope restrictions are not applicable for this token.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php#L91)

#### Returns

`string` &#124; `null` — The token scope defining granted permissions, or null if no scope is specified

#### getToken

```php
public function getToken(): string

```

Get the raw access token value. This method returns the actual token string that was issued by the authentication server. This is the same value returned by __toString() but provided as an explicit getter method for clarity.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php#L102)

#### Returns

`string` — The raw access token value

### Utility

#### isExpired

```php
public function isExpired(): bool

```

Check whether this access token has expired and needs to be refreshed. This method compares the token&#039;s expiration time against the current time to determine if the token is still valid. Expired tokens cannot be used for API requests as they result in authentication failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Authentication/AccessTokenInterface.php#L113)

#### Returns

`bool` — True if the token has expired and should be refreshed, false if still valid
