<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use Override;

/**
 * Maps exception error enums to their default message keys.
 *
 * This class provides the concrete implementation for mapping various error
 * enum types to their corresponding translation message keys. It maintains
 * comprehensive mappings for all error categories in the OpenFGA SDK,
 * enabling consistent and translatable error messages.
 *
 * The class uses static arrays to maintain mappings between error enum values
 * and message keys, providing fast lookup performance while keeping the
 * mappings centralized and maintainable. Each error category has its own
 * mapping array and corresponding method for type-safe access.
 *
 * Error categories supported:
 * - Authentication errors: Token expiration, invalid credentials
 * - Client errors: General validation and usage failures
 * - Configuration errors: Missing PSR components, setup issues
 * - Network errors: HTTP failures, timeouts, connectivity issues
 * - Serialization errors: JSON parsing, schema validation failures
 *
 * @see Messages Message key enumeration
 * @see Translator Translation system
 */
final class DefaultMessages implements DefaultMessagesInterface
{
    /**
     * @var array<string, Messages>
     */
    private const AUTHENTICATION_ERROR_MESSAGES = [
        AuthenticationError::TokenExpired->value => Messages::AUTH_ERROR_TOKEN_EXPIRED,
        AuthenticationError::TokenInvalid->value => Messages::AUTH_ERROR_TOKEN_INVALID,
    ];

    /**
     * @var array<string, Messages>
     */
    private const CLIENT_ERROR_MESSAGES = [
        ClientError::Authentication->value => Messages::CLIENT_ERROR_AUTHENTICATION,
        ClientError::Configuration->value => Messages::CLIENT_ERROR_CONFIGURATION,
        ClientError::Network->value => Messages::CLIENT_ERROR_NETWORK,
        ClientError::Serialization->value => Messages::CLIENT_ERROR_SERIALIZATION,
        ClientError::Validation->value => Messages::CLIENT_ERROR_VALIDATION,
    ];

    /**
     * @var array<string, Messages>
     */
    private const CONFIGURATION_ERROR_MESSAGES = [
        ConfigurationError::HttpClientMissing->value => Messages::CONFIG_ERROR_HTTP_CLIENT_MISSING,
        ConfigurationError::HttpRequestFactoryMissing->value => Messages::CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING,
        ConfigurationError::HttpResponseFactoryMissing->value => Messages::CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING,
        ConfigurationError::HttpStreamFactoryMissing->value => Messages::CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING,
        ConfigurationError::InvalidUrl->value => Messages::CONFIG_ERROR_INVALID_URL,
        ConfigurationError::InvalidLanguage->value => Messages::CONFIG_ERROR_INVALID_LANGUAGE,
        ConfigurationError::InvalidRetryCount->value => Messages::CONFIG_ERROR_INVALID_RETRY_COUNT,
    ];

    /**
     * @var array<string, Messages>
     */
    private const NETWORK_ERROR_MESSAGES = [
        NetworkError::Conflict->value => Messages::NETWORK_ERROR_CONFLICT,
        NetworkError::Forbidden->value => Messages::NETWORK_ERROR_FORBIDDEN,
        NetworkError::Invalid->value => Messages::NETWORK_ERROR_INVALID,
        NetworkError::Request->value => Messages::NETWORK_ERROR_REQUEST,
        NetworkError::Server->value => Messages::NETWORK_ERROR_SERVER,
        NetworkError::Timeout->value => Messages::NETWORK_ERROR_TIMEOUT,
        NetworkError::Unauthenticated->value => Messages::NETWORK_ERROR_UNAUTHENTICATED,
        NetworkError::UndefinedEndpoint->value => Messages::NETWORK_ERROR_UNDEFINED_ENDPOINT,
        NetworkError::Unexpected->value => Messages::NETWORK_ERROR_UNEXPECTED,
    ];

    /**
     * @var array<string, Messages>
     */
    private const SERIALIZATION_ERROR_MESSAGES = [
        SerializationError::CouldNotAddItemsToCollection->value => Messages::SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS,
        SerializationError::EmptyCollection->value => Messages::SERIALIZATION_ERROR_EMPTY_COLLECTION,
        SerializationError::InvalidItemType->value => Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE,
        SerializationError::MissingRequiredConstructorParameter->value => Messages::SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM,
        SerializationError::Response->value => Messages::SERIALIZATION_ERROR_RESPONSE,
        SerializationError::UndefinedItemType->value => Messages::SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE,
    ];

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forAuthenticationError(AuthenticationError $error): Messages
    {
        return self::AUTHENTICATION_ERROR_MESSAGES[$error->value];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forClientError(ClientError $error): Messages
    {
        return self::CLIENT_ERROR_MESSAGES[$error->value];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forConfigurationError(ConfigurationError $error): Messages
    {
        return self::CONFIGURATION_ERROR_MESSAGES[$error->value];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forError(ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError $error): Messages
    {
        return match (true) {
            $error instanceof ClientError => self::forClientError($error),
            $error instanceof AuthenticationError => self::forAuthenticationError($error),
            $error instanceof ConfigurationError => self::forConfigurationError($error),
            $error instanceof NetworkError => self::forNetworkError($error),
            $error instanceof SerializationError => self::forSerializationError($error),
        };
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forNetworkError(NetworkError $error): Messages
    {
        return self::NETWORK_ERROR_MESSAGES[$error->value];
    }

    /**
     * @inheritDoc
     */
    #[Override]
    public static function forSerializationError(SerializationError $error): Messages
    {
        return self::SERIALIZATION_ERROR_MESSAGES[$error->value];
    }
}
