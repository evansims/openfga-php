# ServiceProvider

Service provider for managing OpenFGA service registration and configuration. This provider encapsulates the complex service dependency graph required for the OpenFGA client, providing a clean interface for service registration and retrieval. It manages the lifecycle of all services and ensures proper dependency injection while maintaining performance through lazy loading. The provider supports configuration through various sources and provides both default implementations and the ability to override services with custom implementations for testing or specialized use cases.

## Namespace

`OpenFGA\DI`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProvider.php)

## Implements

* [`ServiceProviderInterface`](ServiceProviderInterface.md)

## Related Classes

* [ServiceProviderInterface](DI/ServiceProviderInterface.md) (interface)
* [ServiceProvider](Integration/ServiceProvider.md) (item)

## Methods

### List Operations

#### get

```php
public function get(string $serviceId): ?object

```

Get a service instance by identifier. Retrieves the service instance for the specified identifier, creating it if necessary using the registered factory. Services are cached after first creation for performance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProvider.php#L122)

#### Parameters

| Name         | Type     | Description            |
| ------------ | -------- | ---------------------- |
| `$serviceId` | `string` | The service identifier |

#### Returns

`object` &#124; `null` — The service instance, or null if the service is optional and not available

### Utility

#### has

```php
public function has(string $serviceId): bool

```

Check if a service is registered. Returns true if the service identifier has been registered with the provider, either as a concrete instance or as a factory function.

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProvider.php#L146)

#### Parameters

| Name         | Type     | Description                     |
| ------------ | -------- | ------------------------------- |
| `$serviceId` | `string` | The service identifier to check |

#### Returns

`bool` — True if the service is registered

#### set

```php
public function set(string $serviceId, object $service): void

```

Register a concrete service instance. Registers a pre-instantiated service instance with the provider. This is useful for registering singletons or test doubles.

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProvider.php#L155)

#### Parameters

| Name         | Type     | Description            |
| ------------ | -------- | ---------------------- |
| `$serviceId` | `string` | The service identifier |
| `$service`   | `object` | The service instance   |

#### Returns

`void`

### Other

#### factory

```php
public function factory(string $serviceId, callable $factory): void

```

Register a service factory function. Registers a factory function that will be called to create the service instance when first requested. The factory should return an object instance of the expected service type.

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProvider.php#L113)

#### Parameters

| Name         | Type       | Description            |
| ------------ | ---------- | ---------------------- |
| `$serviceId` | `string`   | The service identifier |
| `$factory`   | `callable` |                        |

#### Returns

`void`
