# ConfigurationServiceInterface

Service interface for managing client configuration in OpenFGA operations. This service abstracts configuration management from the Client class, handling the complexities of PSR factory discovery, HTTP client configuration, retry policy management, and endpoint validation. It provides a clean interface for accessing configured components while encapsulating configuration validation and default value handling. ## Core Functionality The service manages client configuration including: - PSR HTTP factory discovery and validation (PSR-17, PSR-18) - RequestManager creation with proper configuration - Retry policy validation and normalization - Stream factory management with auto-discovery fallback - Configuration validation and error handling ## Usage Example ```php $configService = new ConfigurationService( url: &#039;https://api.fga.example&#039;, maxRetries: 3, httpClient: $customClient ); Get properly configured components $requestManager = $configService-&gt;getRequestManager($authHeader, $telemetry); $streamFactory = $configService-&gt;getStreamFactory(); ```

## Namespace

`OpenFGA\Services`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php)

## Related Classes

* [ConfigurationService](Services/ConfigurationService.md) (implementation)

## Methods

### Authorization

#### validateConfiguration

```php
public function validateConfiguration(): bool

```

Validate the current configuration. Performs comprehensive validation of all configuration parameters, checking for invalid values, missing required components, and configuration conflicts. This helps catch configuration issues early rather than failing during request processing.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L131)

#### Returns

`bool` — True if configuration is valid

### List Operations

#### getBaseUrl

```php
public function getBaseUrl(): string

```

Get the base API URL for OpenFGA requests. Returns the configured base URL that will be used as the foundation for all API requests. This URL should include the protocol and host, and may include a base path if the API is mounted at a subpath.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L60)

#### Returns

`string` — The base API URL

#### getLanguage

```php
public function getLanguage(): string

```

Get the configured language for internationalization. Returns the language code that should be used for error messages, logging, and any user-facing text. This enables consistent localization across the SDK.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L71)

#### Returns

`string` — The configured language code (e.g., &#039;en&#039;, &#039;es&#039;)

#### getMaxRetries

```php
public function getMaxRetries(): int

```

Get the normalized maximum retry count. Returns the maximum number of retries that should be attempted for failed requests, normalized to a safe range. The value is validated and clamped to prevent excessive retry attempts that could cause performance issues.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L83)

#### Returns

`int` — The normalized retry count (0-15 range)

#### getRequestManager

```php
public function getRequestManager(
    string|null $authorizationHeader = NULL,
    TelemetryInterface|null $telemetry = NULL,
): RequestManager

```

Get or create a configured RequestManager instance. Creates a RequestManager with all the configured HTTP factories, authentication, telemetry, and retry settings. This provides a fully configured HTTP request manager ready for API operations.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L100)

#### Parameters

| Name                   | Type                               | Description                   |
| ---------------------- | ---------------------------------- | ----------------------------- |
| `$authorizationHeader` | `string` &#124; `null`             | Optional authorization header |
| `$telemetry`           | `TelemetryInterface` &#124; `null` | Optional telemetry provider   |

#### Returns

`RequestManager` — The configured request manager

#### getStreamFactory

```php
public function getStreamFactory(): StreamFactoryInterface

```

Get or discover a PSR-17 StreamFactory instance. Returns the configured stream factory, or attempts to discover one using PSR auto-discovery if none was provided. This ensures that stream creation is always available for request body handling.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Services/ConfigurationServiceInterface.php#L115)

#### Returns

`StreamFactoryInterface` — The stream factory instance
