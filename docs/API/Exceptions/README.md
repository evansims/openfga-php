# Exceptions

[API Documentation](../README.md) > Exceptions

Exception hierarchy for type-safe error handling throughout the SDK.

**Total Components:** 13

## Interfaces

| Name | Description |
|------|-------------|
| [`ClientThrowable`](./ClientThrowable.md) | Base interface for all OpenFGA SDK exceptions. Extends the standard PHP Throwable interface with ... |
| [`DefaultMessagesInterface`](./DefaultMessagesInterface.md) | Interface for mapping exception error enums to their default message keys. Provides a contract fo... |

## Classes

| Name | Description |
|------|-------------|
| [`AuthenticationException`](./AuthenticationException.md) | Authentication-related exception for the OpenFGA SDK. Thrown when authentication failures occur, ... |
| [`ClientException`](./ClientException.md) | General client exception for the OpenFGA SDK. Thrown for high-level client errors that can be cat... |
| [`ConfigurationException`](./ConfigurationException.md) | Configuration-related exception for the OpenFGA SDK. Thrown when configuration errors occur, typi... |
| [`DefaultMessages`](./DefaultMessages.md) | Maps exception error enums to their default message keys. This class provides the concrete implem... |
| [`NetworkException`](./NetworkException.md) | Network-related exception for the OpenFGA SDK. Thrown when network or HTTP communication errors o... |
| [`SerializationException`](./SerializationException.md) | Serialization-related exception for the OpenFGA SDK. Thrown when data serialization, deserializat... |

## Enumerations

| Name | Description |
|------|-------------|
| [`AuthenticationError`](./AuthenticationError.md) | Authentication error types for the OpenFGA SDK. Defines specific authentication failure scenarios... |
| [`ClientError`](./ClientError.md) | General client error types for the OpenFGA SDK. Defines high-level error categories that can occu... |
| [`ConfigurationError`](./ConfigurationError.md) | Configuration error types for the OpenFGA SDK. Defines specific configuration-related failures th... |
| [`NetworkError`](./NetworkError.md) | Network error types for the OpenFGA SDK. Defines specific network and HTTP-related failures that ... |
| [`SerializationError`](./SerializationError.md) | Serialization error types for the OpenFGA SDK. Defines specific serialization and data processing... |

---

[← Back to API Documentation](../README.md)
