# Configuration

Configuration value object for OpenFGA client setup. Encapsulates all configuration options for the OpenFGA client to reduce constructor complexity and improve testability. Provides a fluent interface for building configurations and sensible defaults for all optional services. This class follows the builder pattern and immutable value object principles, allowing for easy configuration composition and testing without side effects.

## Namespace

`OpenFGA`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Configuration.php)

## Methods

#### withHttp

```php
public function withHttp(
    ?int $httpMaxRetries = NULL,
    HttpClientInterface|null $httpClient = NULL,
    RequestFactoryInterface|null $httpRequestFactory = NULL,
    ResponseFactoryInterface|null $httpResponseFactory = NULL,
    StreamFactoryInterface|null $httpStreamFactory = NULL,
): self

```

Create a new configuration with custom HTTP settings.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Configuration.php#L107)

#### Parameters

| Name                   | Type                                     | Description                      |
| ---------------------- | ---------------------------------------- | -------------------------------- |
| `$httpMaxRetries`      | `int` &#124; `null`                      | Optional max retry count         |
| `$httpClient`          | `HttpClientInterface` &#124; `null`      | Optional PSR-18 HTTP client      |
| `$httpRequestFactory`  | `RequestFactoryInterface` &#124; `null`  | Optional PSR-17 request factory  |
| `$httpResponseFactory` | `ResponseFactoryInterface` &#124; `null` | Optional PSR-17 response factory |
| `$httpStreamFactory`   | `StreamFactoryInterface` &#124; `null`   | Optional PSR-17 stream factory   |

#### Returns

`self` — A new configuration instance with HTTP settings

#### withRepositories

```php
public function withRepositories(
    StoreRepositoryInterface|null $storeRepository = NULL,
    ModelRepositoryInterface|null $modelRepository = NULL,
    TupleRepositoryInterface|null $tupleRepository = NULL,
    AssertionRepositoryInterface|null $assertionRepository = NULL,
): self

```

Create a new configuration with custom repositories.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Configuration.php#L147)

#### Parameters

| Name                   | Type                                         | Description                   |
| ---------------------- | -------------------------------------------- | ----------------------------- |
| `$storeRepository`     | `StoreRepositoryInterface` &#124; `null`     | Optional store repository     |
| `$modelRepository`     | `ModelRepositoryInterface` &#124; `null`     | Optional model repository     |
| `$tupleRepository`     | `TupleRepositoryInterface` &#124; `null`     | Optional tuple repository     |
| `$assertionRepository` | `AssertionRepositoryInterface` &#124; `null` | Optional assertion repository |

#### Returns

`self` — A new configuration instance with custom repositories

#### withServices

```php
public function withServices(
    TelemetryInterface|null $telemetry = NULL,
    TupleFilterServiceInterface|null $tupleFilterService = NULL,
    TelemetryServiceInterface|null $telemetryService = NULL,
    AssertionServiceInterface|null $assertionService = NULL,
    AuthorizationServiceInterface|null $authorizationService = NULL,
    HttpServiceInterface|null $httpService = NULL,
    AuthenticationServiceInterface|null $authenticationService = NULL,
    ConfigurationServiceInterface|null $configurationService = NULL,
): self

```

Create a new configuration with custom services.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Configuration.php#L190)

#### Parameters

| Name                     | Type                                           | Description                     |
| ------------------------ | ---------------------------------------------- | ------------------------------- |
| `$telemetry`             | `TelemetryInterface` &#124; `null`             | Optional telemetry provider     |
| `$tupleFilterService`    | `TupleFilterServiceInterface` &#124; `null`    | Optional tuple filter service   |
| `$telemetryService`      | `TelemetryServiceInterface` &#124; `null`      | Optional telemetry service      |
| `$assertionService`      | `AssertionServiceInterface` &#124; `null`      | Optional assertion service      |
| `$authorizationService`  | `AuthorizationServiceInterface` &#124; `null`  | Optional authorization service  |
| `$httpService`           | `HttpServiceInterface` &#124; `null`           | Optional HTTP service           |
| `$authenticationService` | `AuthenticationServiceInterface` &#124; `null` | Optional authentication service |
| `$configurationService`  | `ConfigurationServiceInterface` &#124; `null`  | Optional configuration service  |

#### Returns

`self` — A new configuration instance with custom services
