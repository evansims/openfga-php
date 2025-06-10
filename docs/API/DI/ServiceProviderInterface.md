# ServiceProviderInterface

Interface for dependency injection service providers. This interface defines the contract for service providers that manage service registration, instantiation, and retrieval in the OpenFGA SDK. It provides a clean abstraction for dependency injection while maintaining simplicity and performance. Implementations should provide lazy loading of services and support both singleton and factory patterns for service creation.

## Namespace

`OpenFGA\DI`

## Source

[View source code](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProviderInterface.php)

## Related Classes

* [ServiceProvider](DI/ServiceProvider.md) (implementation)
* [ServiceProvider](Integration/ServiceProvider.md) (implementation)

## Methods

### List Operations

#### get

```php
public function get(string $serviceId): object|null

```

Get a service instance by identifier. Retrieves the service instance for the specified identifier, creating it if necessary using the registered factory. Services are cached after first creation for performance.

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProviderInterface.php#L45)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProviderInterface.php#L56)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProviderInterface.php#L67)

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

[View source](https://github.com/evansims/openfga-php/blob/main/src/DI/ServiceProviderInterface.php#L30)

#### Parameters

| Name         | Type       | Description            |
| ------------ | ---------- | ---------------------- |
| `$serviceId` | `string`   | The service identifier |
| `$factory`   | `callable` |                        |

#### Returns

`void`
