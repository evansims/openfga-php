# ClientFactory

Factory for creating properly configured Client instances with dependency injection. This factory encapsulates the complex object graph creation required for the OpenFGA Client, providing a clean, simple API while maintaining full control over service instantiation. It follows the factory pattern to reduce constructor complexity and improve testability. The factory creates a complete dependency graph: - Infrastructure services (Configuration, Authentication, HTTP, Telemetry) - Repository layer (Store, Model, Tuple, Assertion repositories) - Application services (Authorization, Store, Model, Tuple, Assertion services) - Domain services (Schema validation, tuple filtering)

## Namespace

`OpenFGA\Factories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Factories/ClientFactory.php)

## Implements

* [`ClientFactoryInterface`](ClientFactoryInterface.md)

## Related Classes

* [ClientFactoryInterface](Factories/ClientFactoryInterface.md) (interface)

## Methods

#### create

```php
public function create(
    string $url,
    string|null $storeId = NULL,
    AuthenticationInterface|null $authentication = NULL,
    TelemetryInterface|null $telemetry = NULL,
    array<string, mixed> $options = [],
): ClientInterface

```

Create a fully configured OpenFGA client instance. This is the primary factory method that creates a complete OpenFGA client with all required services and repositories properly configured. It handles the complex dependency graph while providing a simple, clean API.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Factories/ClientFactory.php#L74)

#### Parameters

| Name              | Type                                                                                 | Description                                      |
| ----------------- | ------------------------------------------------------------------------------------ | ------------------------------------------------ |
| `$url`            | `string`                                                                             | The OpenFGA server URL                           |
| `$storeId`        | `string` &#124; `null`                                                               | The default store ID for operations (optional)   |
| `$authentication` | [`AuthenticationInterface`](Authentication/AuthenticationInterface.md) &#124; `null` | Authentication provider (optional)               |
| `$telemetry`      | `TelemetryInterface` &#124; `null`                                                   | Telemetry provider (optional, defaults to no-op) |
| `$options`        | `array&lt;`string`, `mixed`&gt;`                                                     |                                                  |

#### Returns

[`ClientInterface`](ClientInterface.md) — A fully configured OpenFGA client instance
