<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{
    AuthenticationError,
    ClientError,
    ConfigurationError,
    DefaultMessages,
    NetworkError,
    SerializationError
};
use OpenFGA\Messages;

describe('DefaultMessages', function (): void {
    describe('forClientError()', function (): void {
        it('maps client error enums to their default message keys', function (): void {
            expect(DefaultMessages::forClientError(ClientError::Authentication))
                ->toBe(Messages::CLIENT_ERROR_AUTHENTICATION);

            expect(DefaultMessages::forClientError(ClientError::Configuration))
                ->toBe(Messages::CLIENT_ERROR_CONFIGURATION);

            expect(DefaultMessages::forClientError(ClientError::Network))
                ->toBe(Messages::CLIENT_ERROR_NETWORK);

            expect(DefaultMessages::forClientError(ClientError::Serialization))
                ->toBe(Messages::CLIENT_ERROR_SERIALIZATION);

            expect(DefaultMessages::forClientError(ClientError::Validation))
                ->toBe(Messages::CLIENT_ERROR_VALIDATION);
        });
    });

    describe('forAuthenticationError()', function (): void {
        it('maps authentication error enums to their default message keys', function (): void {
            expect(DefaultMessages::forAuthenticationError(AuthenticationError::TokenExpired))
                ->toBe(Messages::AUTH_ERROR_TOKEN_EXPIRED);

            expect(DefaultMessages::forAuthenticationError(AuthenticationError::TokenInvalid))
                ->toBe(Messages::AUTH_ERROR_TOKEN_INVALID);
        });
    });

    describe('forConfigurationError()', function (): void {
        it('maps configuration error enums to their default message keys', function (): void {
            expect(DefaultMessages::forConfigurationError(ConfigurationError::HttpClientMissing))
                ->toBe(Messages::CONFIG_ERROR_HTTP_CLIENT_MISSING);

            expect(DefaultMessages::forConfigurationError(ConfigurationError::HttpRequestFactoryMissing))
                ->toBe(Messages::CONFIG_ERROR_HTTP_REQUEST_FACTORY_MISSING);

            expect(DefaultMessages::forConfigurationError(ConfigurationError::HttpResponseFactoryMissing))
                ->toBe(Messages::CONFIG_ERROR_HTTP_RESPONSE_FACTORY_MISSING);

            expect(DefaultMessages::forConfigurationError(ConfigurationError::HttpStreamFactoryMissing))
                ->toBe(Messages::CONFIG_ERROR_HTTP_STREAM_FACTORY_MISSING);
        });
    });

    describe('forNetworkError()', function (): void {
        it('maps network error enums to their default message keys', function (): void {
            expect(DefaultMessages::forNetworkError(NetworkError::Conflict))
                ->toBe(Messages::NETWORK_ERROR_CONFLICT);

            expect(DefaultMessages::forNetworkError(NetworkError::Forbidden))
                ->toBe(Messages::NETWORK_ERROR_FORBIDDEN);

            expect(DefaultMessages::forNetworkError(NetworkError::Invalid))
                ->toBe(Messages::NETWORK_ERROR_INVALID);

            expect(DefaultMessages::forNetworkError(NetworkError::Request))
                ->toBe(Messages::NETWORK_ERROR_REQUEST);

            expect(DefaultMessages::forNetworkError(NetworkError::Server))
                ->toBe(Messages::NETWORK_ERROR_SERVER);

            expect(DefaultMessages::forNetworkError(NetworkError::Timeout))
                ->toBe(Messages::NETWORK_ERROR_TIMEOUT);

            expect(DefaultMessages::forNetworkError(NetworkError::Unauthenticated))
                ->toBe(Messages::NETWORK_ERROR_UNAUTHENTICATED);

            expect(DefaultMessages::forNetworkError(NetworkError::UndefinedEndpoint))
                ->toBe(Messages::NETWORK_ERROR_UNDEFINED_ENDPOINT);

            expect(DefaultMessages::forNetworkError(NetworkError::Unexpected))
                ->toBe(Messages::NETWORK_ERROR_UNEXPECTED);
        });
    });

    describe('forSerializationError()', function (): void {
        it('maps serialization error enums to their default message keys', function (): void {
            expect(DefaultMessages::forSerializationError(SerializationError::CouldNotAddItemsToCollection))
                ->toBe(Messages::SERIALIZATION_ERROR_COULD_NOT_ADD_ITEMS);

            expect(DefaultMessages::forSerializationError(SerializationError::EmptyCollection))
                ->toBe(Messages::SERIALIZATION_ERROR_EMPTY_COLLECTION);

            expect(DefaultMessages::forSerializationError(SerializationError::InvalidItemType))
                ->toBe(Messages::SERIALIZATION_ERROR_INVALID_ITEM_TYPE);

            expect(DefaultMessages::forSerializationError(SerializationError::MissingRequiredConstructorParameter))
                ->toBe(Messages::SERIALIZATION_ERROR_MISSING_REQUIRED_PARAM);

            expect(DefaultMessages::forSerializationError(SerializationError::Response))
                ->toBe(Messages::SERIALIZATION_ERROR_RESPONSE);

            expect(DefaultMessages::forSerializationError(SerializationError::UndefinedItemType))
                ->toBe(Messages::SERIALIZATION_ERROR_UNDEFINED_ITEM_TYPE);
        });
    });

    describe('forError()', function (): void {
        it('handles different error types using match expression', function (): void {
            // ClientError
            expect(DefaultMessages::forError(ClientError::Validation))
                ->toBe(Messages::CLIENT_ERROR_VALIDATION);

            // AuthenticationError
            expect(DefaultMessages::forError(AuthenticationError::TokenExpired))
                ->toBe(Messages::AUTH_ERROR_TOKEN_EXPIRED);

            // ConfigurationError
            expect(DefaultMessages::forError(ConfigurationError::HttpClientMissing))
                ->toBe(Messages::CONFIG_ERROR_HTTP_CLIENT_MISSING);

            // NetworkError
            expect(DefaultMessages::forError(NetworkError::Server))
                ->toBe(Messages::NETWORK_ERROR_SERVER);

            // SerializationError
            expect(DefaultMessages::forError(SerializationError::Response))
                ->toBe(Messages::SERIALIZATION_ERROR_RESPONSE);
        });
    });
});
