# OpenFGA PHP SDK

The `evansims/openfga-php` package is an unofficial/experimental PHP SDK for [OpenFGA](https://openfga.dev/) and [Okta FGA](https://www.okta.com/products/fine-grained-authorization/). It's fast, lightweight, and easy to use.

Features:

- All OpenFGA API endpoints are supported
- Okta FGA and OpenFGA authentication support, including ODIC client credentials and shared key authentication
- Uses PHP-FIG interoperability standards for network requests (PSR-7, PSR-17, and PSR-18)

## Requirements

- [PHP 8.4+](https://www.php.net/)
- [Composer 2](https://getcomposer.org/)
- Access to an [OpenFGA](https://openfga.dev/docs/getting-started/setup-openfga/overview) or [Okta FGA instance](http://dashboard.fga.dev/)

## Installation

```bash
composer require evansims/openfga-php
```

> [!IMPORTANT]
> Your application must fulfill the PSR-7, PSR-17, and PSR-18 implementations. If you're unable to install the SDK due to a missing implementation, first install any libraries of your preference that implement those interfaces, and then retry. For example: `composer require kriswallsmith/buzz nyholm/psr7`.

## Usage

### Credential Configuration

> [!NOTE]
> If you're [not using authentication](https://openfga.dev/docs/getting-started/setup-sdk-client#using-no-authentication), you can ignore this step.

The SDK supports two types of credentials: [ODIC](https://openfga.dev/docs/getting-started/setup-sdk-client#using-client-credentials-flow) and [shared key](https://openfga.dev/docs/getting-started/setup-sdk-client#using-shared-key-authentication). ODIC ("client credentials flow") credentials are used to authenticate with an Okta FGA instance, while shared key credentials can be used to authenticate with an OpenFGA instance.

To configure the SDK with your credentials, create an appropriate `Credentials` class instance for your authentication type. Later on, you'll pass this instance to the `ClientConfiguration` constructor as the `credentialConfiguration` parameter.

For ODIC ("Client Credentials flow") credentials:

```php
use OpenFGA\SDK\Configuration\Credentials\ClientCredentialConfiguration;

$credential = new ClientCredentialConfiguration(
    apiIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? null,
    apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? null,
    clientId: $_ENV['FGA_CLIENT_ID'] ?? null,
    clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? null,
);
```

Or, when using a shared key:

```php
use OpenFGA\SDK\Configuration\Credentials\SharedKeyCredentialConfiguration;

$credential = new SharedKeyCredentialConfiguration(
    sharedKey: $_ENV['FGA_SHARED_KEY'] ?? null,
);
```

### Client Configuration

Next, create a `ClientConfiguration` instance. This will be used to configure the SDK client, including the base URL of the FGA instance you're connecting to, the credentials you've configured, and any other options you'd like to set. For example:

```php
use OpenFGA\SDK\Configuration\ClientConfiguration;

$configuration = new ClientConfiguration(
    apiUrl: $_ENV['FGA_API_URL'] ?? null,
    storeId: $_ENV['FGA_STORE_ID'] ?? null,
    authorizationModelId: $_ENV['FGA_MODEL_ID'] ?? null,
    credentialConfiguration: $credential, // Pass the credential object you created in the previous step
);
```

### Client Initialization

Finally, create a `OpenFGA\Client` instance using the configuration you've set up:

```php
use OpenFGA\Client;

$client = new Client($configuration);
```

All set! You're now ready to start making requests to the OpenFGA API.

### Making Requests

TODO: Add examples of making requests
