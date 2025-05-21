# Authentication

Authentication is configured when creating a `Client`. Three approaches are available:

| Option | Description |
| ------ | ----------- |
| `Authentication::NONE` | No authentication. |
| `Authentication::TOKEN` | Use a pre-shared access token. Provide the token as `token:` when creating the client. |
| `Authentication::CLIENT_CREDENTIALS` | Use OAuth client credentials. Provide `clientId`, `clientSecret`, `issuer` and `audience` when creating the client. |

`AccessTokenInterface` defines the shape of an OAuth token. `ClientCredentialAuthentication` implements the `AuthenticationInterface` to request tokens using client credentials.
