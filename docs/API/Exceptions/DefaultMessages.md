# DefaultMessages

Maps exception error enums to their default message keys. This class provides the concrete implementation for mapping various error enum types to their corresponding translation message keys. It maintains comprehensive mappings for all error categories in the OpenFGA SDK, enabling consistent and translatable error messages. The class uses static arrays to maintain mappings between error enum values and message keys, providing fast lookup performance while keeping the mappings centralized and maintainable. Each error category has its own mapping array and corresponding method for type-safe access. Error categories supported: - Authentication errors: Token expiration, invalid credentials - Client errors: General validation and usage failures - Configuration errors: Missing PSR components, setup issues - Network errors: HTTP failures, timeouts, connectivity issues - Serialization errors: JSON parsing, schema validation failures

## Namespace
`OpenFGA\Exceptions`

## Source
[View source code](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessages.php)

## Implements
* [`DefaultMessagesInterface`](DefaultMessagesInterface.md)

## Related Classes
* [DefaultMessagesInterface](Exceptions/DefaultMessagesInterface.md) (interface)

## Methods

#### forAuthenticationError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forAuthenticationError(AuthenticationError $error): Messages
```

Get the default message key for an authentication error. Maps authentication-related error types (such as expired tokens or invalid credentials) to their corresponding message keys. These messages typically guide users on how to resolve authentication issues with the OpenFGA service.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L40)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`AuthenticationError`](AuthenticationError.md) | The specific authentication error type that occurred |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
#### forClientError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forClientError(ClientError $error): Messages
```

Get the default message key for a general client error. Maps high-level client error categories to their corresponding message keys. These are broad error classifications that encompass various types of SDK usage and operational failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L52)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`ClientError`](ClientError.md) | The specific client error type that occurred |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
#### forConfigurationError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forConfigurationError(ConfigurationError $error): Messages
```

Get the default message key for a configuration error. Maps configuration-related error types (such as missing PSR components or invalid setup) to their corresponding message keys. These messages typically provide guidance on proper SDK configuration and setup.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L64)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`ConfigurationError`](ConfigurationError.md) | The specific configuration error type that occurred |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
#### forError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forError(
    AuthenticationError|ClientError|ConfigurationError|NetworkError|SerializationError $error,
): Messages
```

Get the default message key for any supported error type. Generic method that accepts any error enum type and routes it to the appropriate specific method. This provides a unified interface for error message lookup when the specific error type is not known at compile time.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L77)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`AuthenticationError`](AuthenticationError.md) &#124; [`ClientError`](ClientError.md) &#124; [`ConfigurationError`](ConfigurationError.md) &#124; [`NetworkError`](NetworkError.md) &#124; [`SerializationError`](SerializationError.md) | The error enum of any supported type |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
#### forNetworkError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forNetworkError(NetworkError $error): Messages
```

Get the default message key for a network error. Maps network and HTTP-related error types (such as timeouts, HTTP status codes, or connectivity issues) to their corresponding message keys. These messages often include information about retry strategies and network troubleshooting.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L90)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`NetworkError`](NetworkError.md) | The specific network error type that occurred |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
#### forSerializationError

*<small>Implements Exceptions\DefaultMessagesInterface</small>*

```php
public function forSerializationError(SerializationError $error): Messages
```

Get the default message key for a serialization error. Maps data serialization and validation error types (such as JSON parsing failures or schema validation errors) to their corresponding message keys. These messages typically provide details about data format issues and validation failures.

[View source](https://github.com/evansims/openfga-php/blob/main/src/Exceptions/DefaultMessagesInterface.php#L103)

#### Parameters
| Name | Type | Description |
|------|------|-------------|
| `$error` | [`SerializationError`](SerializationError.md) | The specific serialization error type that occurred |

#### Returns
[`Messages`](Messages.md) — The corresponding message enum case for translation
