# ServiceProvider

Service provider for automatic dependency injection container registration. This class enables automatic registration of OpenFGA services in frameworks that support the tbachert/spi service provider interface pattern.

## Namespace
`OpenFGA\Integration`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Integration/ServiceProvider.php)

## Methods

#### register

```php
public function register(object $container): void
```

Register OpenFGA services with the dependency injection container. This method registers the core OpenFGA interfaces with their default implementations, enabling automatic dependency resolution in SPI-compatible frameworks. Note: This service provider registers basic implementations that work without configuration. For production use, you should override these registrations with properly configured instances. Services registered: - TelemetryInterface: No-op telemetry provider (can be overridden) - TransformerInterface: DSL to model transformation - SchemaValidatorInterface: JSON schema validation for models Services NOT registered (require configuration): - ClientInterface: Requires URL and authentication configuration - RequestManagerInterface: Requires URL and retry configuration

[View source](https://github.com/evansims/openfga-php/blob/main/src/Integration/ServiceProvider.php#L45)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$container` | `object` | The dependency injection container |

#### Returns
`void`
