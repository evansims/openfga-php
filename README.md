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
> Your application must already fulfill (or have dependencies installed that fulfill) the PSR-7, PSR-17, and PSR-18 implementations. You can install any libraries of your preference that implement these interfaces; for example: `composer require kriswallsmith/buzz nyholm/psr7 evansims/openfga-php`.

## Usage

### Credential Configuration

> [!NOTE]
> If you're [not using authentication](https://openfga.dev/docs/getting-started/setup-sdk-client#using-no-authentication), you can ignore this step.

The SDK supports three types of credentials: ODIC and shared key. ODIC credentials are used to authenticate with an Okta FGA instance, while shared key credentials can be used to authenticate with an OpenFGA instance. To configure the SDK with your credentials, create an appropriate `Credentials` object. You'll ultimately pass it to the `ClientConfiguration` constructor as the `credentialConfiguration` parameter.

For ODIC credentials:

```php
$credential = new ClientCredentialConfiguration(
    apiIssuer: $_ENV['FGA_API_TOKEN_ISSUER'] ?? null,
    apiAudience: $_ENV['FGA_API_AUDIENCE'] ?? null,
    clientId: $_ENV['FGA_CLIENT_ID'] ?? null,
    clientSecret: $_ENV['FGA_CLIENT_SECRET'] ?? null,
);
```

Or, when using a shared key:

```php
$credential = new SharedKeyCredentialConfiguration(
    sharedKey: $_ENV['FGA_SHARED_KEY'] ?? null,
);
```

### Client Configuration

Next, create a `ClientConfiguration` object. This object is used to configure the SDK client, including the base URL of the FGA instance you're connecting to, the credentials you've configured, and any other options you'd like to set. For example:

```php
$configuration = new ClientConfiguration(
    apiUrl: $_ENV['FGA_API_URL'] ?? null,
    storeId: $_ENV['FGA_STORE_ID'] ?? null,
    authorizationModelId: $_ENV['FGA_MODEL_ID'] ?? null,
    credentialConfiguration: $credential,
);
```

### Client Initialization

Finally, create a `Client` object using the configuration you've set up:

```php
$client = new Client($configuration);
```

### Making Requests

TODO: Add examples of making requests
