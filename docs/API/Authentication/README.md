# Authentication

[API Documentation](../README.md) > Authentication

Authentication providers and token management for OpenFGA API access.

**Total Components:** 5

## Interfaces

| Name | Description |
|------|-------------|
| [`AccessTokenInterface`](./AccessTokenInterface.md) | Represents an access token for OpenFGA API authentication. Access tokens are credentials used to ... |
| [`AuthenticationInterface`](./AuthenticationInterface.md) | Interface for OpenFGA authentication strategies. This interface defines the contract for differen... |

## Classes

| Name | Description |
|------|-------------|
| [`AccessToken`](./AccessToken.md) | Immutable access token implementation for OpenFGA API authentication. This class represents an OA... |
| [`ClientCredentialAuthentication`](./ClientCredentialAuthentication.md) | OAuth 2.0 Client Credentials authentication strategy for OpenFGA client. This authentication stra... |
| [`TokenAuthentication`](./TokenAuthentication.md) | Token-based authentication strategy for OpenFGA client. This authentication strategy uses a pre-s... |

---

[← Back to API Documentation](../README.md)
