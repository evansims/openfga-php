# ConfigurationService

Service implementation for managing client configuration in OpenFGA operations. This service encapsulates all configuration-related logic, providing a clean abstraction over HTTP factory management, retry policies, URL handling, and PSR component discovery. It handles the complexities of configuration validation and provides safe defaults while maintaining flexibility for custom configurations. The service follows the builder pattern for configuration management and provides lazy initialization of expensive components like RequestManager instances. All configuration is validated during service creation to fail fast on invalid configurations.

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php)

## Implements

* [`ConfigurationServiceInterface`](ConfigurationServiceInterface.md)

## Related Classes

* [ConfigurationServiceInterface](Services/ConfigurationServiceInterface.md) (interface)

## Methods

### Authorization

#### validateConfiguration

```php
public function validateConfiguration(): bool

```

Validate the current configuration. Performs comprehensive validation of all configuration parameters, checking for invalid values, missing required components, and configuration conflicts. This helps catch configuration issues early rather than failing during request processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L132)

#### Returns

`bool` — True if configuration is valid

### List Operations

#### getBaseUrl

```php
public function getBaseUrl(): string

```

Get the base API URL for OpenFGA requests. Returns the configured base URL that will be used as the foundation for all API requests. This URL should include the protocol and host, and may include a base path if the API is mounted at a subpath.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L65)

#### Returns

`string` — The base API URL

#### getLanguage

```php
public function getLanguage(): string

```

Get the configured language for internationalization. Returns the language code that should be used for error messages, logging, and any user-facing text. This enables consistent localization across the SDK.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L74)

#### Returns

`string` — The configured language code (e.g., &#039;en&#039;, &#039;es&#039;)

#### getMaxRetries

```php
public function getMaxRetries(): int

```

Get the normalized maximum retry count. Returns the maximum number of retries that should be attempted for failed requests, normalized to a safe range. The value is validated and clamped to prevent excessive retry attempts that could cause performance issues.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L83)

#### Returns

`int` — The normalized retry count (0-15 range)

#### getRequestManager

```php
public function getRequestManager(
    ?string $authorizationHeader = NULL,
    ?OpenFGA\Observability\TelemetryInterface $telemetry = NULL,
): OpenFGA\Network\RequestManager

```

Get or create a configured RequestManager instance. Creates a RequestManager with all the configured HTTP factories, authentication, telemetry, and retry settings. This provides a fully configured HTTP request manager ready for API operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L93)

#### Parameters

| Name                   | Type                                                                      | Description                   |
| ---------------------- | ------------------------------------------------------------------------- | ----------------------------- |
| `$authorizationHeader` | `string` &#124; `null`                                                    | Optional authorization header |
| `$telemetry`           | [`TelemetryInterface`](Observability/TelemetryInterface.md) &#124; `null` | Optional telemetry provider   |

#### Returns

[`RequestManager`](Network/RequestManager.md) — The configured request manager

#### getStreamFactory

```php
public function getStreamFactory(): Psr\Http\Message\StreamFactoryInterface

```

Get or discover a PSR-17 StreamFactory instance. Returns the configured stream factory, or attempts to discover one using PSR auto-discovery if none was provided. This ensures that stream creation is always available for request body handling.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationService.php#L111)

#### Returns

`StreamFactoryInterface` — The stream factory instance
