<?php

declare(strict_types=1);

namespace OpenFGA\Exceptions;

use OpenFGA\Messages;
use OpenFGA\Translation\Translator;

/**
 * Interface for mapping exception error enums to their default message keys.
 *
 * Provides a contract for retrieving default translation message keys
 * for various types of exceptions in the OpenFGA SDK. This interface
 * enables consistent error messaging across the SDK by centralizing
 * the mapping between error types and their corresponding human-readable
 * messages, supporting internationalization and localization.
 *
 * The interface handles all major error categories in the OpenFGA SDK:
 * authentication, client, configuration, network, and serialization errors.
 * Each method returns a Messages enum that can be used with the translation
 * system to generate localized error messages.
 *
 * @see Messages Message key enumeration
 * @see Translator Translation system
 */
interface DefaultMessagesInterface
{
    /**
     * Get the default message key for an authentication error.
     *
     * Maps authentication-related error types (such as expired tokens or
     * invalid credentials) to their corresponding message keys. These messages
     * typically guide users on how to resolve authentication issues with
     * the OpenFGA service.
     *
     * @param  AuthenticationError $error The specific authentication error type that occurred
     * @return Messages            The corresponding message enum case for translation
     */
    public static function forAuthenticationError(AuthenticationError $error): Messages;

    /**
     * Get the default message key for a general client error.
     *
     * Maps high-level client error categories to their corresponding message keys.
     * These are broad error classifications that encompass various types of
     * SDK usage and operational failures.
     *
     * @param  ClientError $error The specific client error type that occurred
     * @return Messages    The corresponding message enum case for translation
     */
    public static function forClientError(ClientError $error): Messages;

    /**
     * Get the default message key for a configuration error.
     *
     * Maps configuration-related error types (such as missing PSR components
     * or invalid setup) to their corresponding message keys. These messages
     * typically provide guidance on proper SDK configuration and setup.
     *
     * @param  ConfigurationError $error The specific configuration error type that occurred
     * @return Messages           The corresponding message enum case for translation
     */
    public static function forConfigurationError(ConfigurationError $error): Messages;

    /**
     * Get the default message key for any supported error type.
     *
     * Generic method that accepts any error enum type and routes it to the
     * appropriate specific method. This provides a unified interface for
     * error message lookup when the specific error type is not known at
     * compile time.
     *
     * @param  AuthenticationError|ClientError|ConfigurationError|NetworkError|SerializationError $error The error enum of any supported type
     * @return Messages                                                                           The corresponding message enum case for translation
     */
    public static function forError(ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError $error): Messages;

    /**
     * Get the default message key for a network error.
     *
     * Maps network and HTTP-related error types (such as timeouts, HTTP status
     * codes, or connectivity issues) to their corresponding message keys. These
     * messages often include information about retry strategies and network
     * troubleshooting.
     *
     * @param  NetworkError $error The specific network error type that occurred
     * @return Messages     The corresponding message enum case for translation
     */
    public static function forNetworkError(NetworkError $error): Messages;

    /**
     * Get the default message key for a serialization error.
     *
     * Maps data serialization and validation error types (such as JSON parsing
     * failures or schema validation errors) to their corresponding message keys.
     * These messages typically provide details about data format issues and
     * validation failures.
     *
     * @param  SerializationError $error The specific serialization error type that occurred
     * @return Messages           The corresponding message enum case for translation
     */
    public static function forSerializationError(SerializationError $error): Messages;
}
