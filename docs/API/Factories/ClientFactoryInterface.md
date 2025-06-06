# ClientFactoryInterface

Interface for OpenFGA client factories. This interface defines the contract for factories that create properly configured OpenFGA client instances. It enables dependency injection of different factory implementations and supports testing with mock factories. Implementations should handle the complex object graph creation required for the OpenFGA Client while providing a clean, simple API for consumers.

## Namespace

`OpenFGA\Factories`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/Factories/ClientFactoryInterface.php)

## Related Classes

* [ClientFactory](Factories/ClientFactory.md) (implementation)

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

Create a fully configured OpenFGA client instance. This method should create a complete OpenFGA client with all required services and repositories properly configured. It should handle the complex dependency graph while providing a simple, clean API.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Factories/ClientFactoryInterface.php#L40)

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
