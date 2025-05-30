<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{
    AuthenticationError,
    AuthenticationException,
    ClientError,
    ClientException,
    ConfigurationError,
    ConfigurationException,
    NetworkError,
    NetworkException,
    SerializationError,
    SerializationException
};
use OpenFGA\Translation\Translator;

beforeEach(function (): void {
    // Reset translator to ensure consistent state
    Translator::reset();
});

describe('Exception default message behavior', function (): void {
    describe('ClientException', function (): void {
        it('uses default message when no message is provided', function (): void {
            $exception = ClientError::Validation->exception();

            expect($exception)
                ->toBeInstanceOf(ClientException::class)
                ->and($exception->getMessage())
                ->toBe('Request validation failed');
        });

        it('uses provided message when available', function (): void {
            $customMessage = 'Custom validation error message';
            $exception = ClientError::Validation->exception(
                context: ['message' => $customMessage],
            );

            expect($exception->getMessage())->toBe($customMessage);
        });
    });

    describe('AuthenticationException', function (): void {
        it('uses default message when no message is provided', function (): void {
            $exception = AuthenticationError::TokenExpired->exception();

            expect($exception)
                ->toBeInstanceOf(AuthenticationException::class)
                ->and($exception->getMessage())
                ->toBe('Authentication token has expired');
        });

        it('uses provided message when available', function (): void {
            $customMessage = 'Your session has timed out';
            $exception = AuthenticationError::TokenExpired->exception(
                context: ['message' => $customMessage],
            );

            expect($exception->getMessage())->toBe($customMessage);
        });
    });

    describe('ConfigurationException', function (): void {
        it('uses default message when no message is provided', function (): void {
            $exception = ConfigurationError::HttpClientMissing->exception();

            expect($exception)
                ->toBeInstanceOf(ConfigurationException::class)
                ->and($exception->getMessage())
                ->toBe('HTTP client is not configured');
        });

        it('uses provided message when available', function (): void {
            $customMessage = 'PSR-18 HTTP client required';
            $exception = ConfigurationError::HttpClientMissing->exception(
                context: ['message' => $customMessage],
            );

            expect($exception->getMessage())->toBe($customMessage);
        });
    });

    describe('NetworkException', function (): void {
        it('uses default message when no message is provided', function (): void {
            $exception = NetworkError::Forbidden->exception();

            expect($exception)
                ->toBeInstanceOf(NetworkException::class)
                ->and($exception->getMessage())
                ->toBe('Forbidden (403): Access denied to the requested resource');
        });

        it('uses provided message when available', function (): void {
            $customMessage = 'You do not have permission to access this resource';
            $exception = NetworkError::Forbidden->exception(
                context: ['message' => $customMessage],
            );

            expect($exception->getMessage())->toBe($customMessage);
        });
    });

    describe('SerializationException', function (): void {
        it('uses default message when no message is provided', function (): void {
            $exception = SerializationError::Response->exception();

            expect($exception)
                ->toBeInstanceOf(SerializationException::class)
                ->and($exception->getMessage())
                ->toBe('Failed to serialize/deserialize response data');
        });

        it('uses provided message when available', function (): void {
            $customMessage = 'JSON decode error: Syntax error';
            $exception = SerializationError::Response->exception(
                context: ['message' => $customMessage],
            );

            expect($exception->getMessage())->toBe($customMessage);
        });
    });

    describe('Edge cases', function (): void {
        it('falls back to default when empty string is provided', function (): void {
            $exception = ClientError::Network->exception(
                context: ['message' => ''],
            );

            expect($exception->getMessage())->toBe('Network communication error');
        });

        it('falls back to default when message key is missing', function (): void {
            $exception = ClientError::Network->exception(
                context: ['other_key' => 'some value'],
            );

            expect($exception->getMessage())->toBe('Network communication error');
        });
    });
});

describe('All error enums have default messages', function (): void {
    it('provides default messages for all ClientError variants', function (): void {
        expect(ClientError::Authentication->exception()->getMessage())
            ->toBe('Authentication error occurred');
        expect(ClientError::Configuration->exception()->getMessage())
            ->toBe('Configuration error detected');
        expect(ClientError::Network->exception()->getMessage())
            ->toBe('Network communication error');
        expect(ClientError::Serialization->exception()->getMessage())
            ->toBe('Data serialization error');
        expect(ClientError::Validation->exception()->getMessage())
            ->toBe('Request validation failed');
    });

    it('provides default messages for all AuthenticationError variants', function (): void {
        expect(AuthenticationError::TokenExpired->exception()->getMessage())
            ->toBe('Authentication token has expired');
        expect(AuthenticationError::TokenInvalid->exception()->getMessage())
            ->toBe('Authentication token is invalid');
    });

    it('provides default messages for all ConfigurationError variants', function (): void {
        expect(ConfigurationError::HttpClientMissing->exception()->getMessage())
            ->toBe('HTTP client is not configured');
        expect(ConfigurationError::HttpRequestFactoryMissing->exception()->getMessage())
            ->toBe('HTTP request factory is not configured');
        expect(ConfigurationError::HttpResponseFactoryMissing->exception()->getMessage())
            ->toBe('HTTP response factory is not configured');
        expect(ConfigurationError::HttpStreamFactoryMissing->exception()->getMessage())
            ->toBe('HTTP stream factory is not configured');
    });

    it('provides default messages for all NetworkError variants', function (): void {
        expect(NetworkError::Conflict->exception()->getMessage())
            ->toBe('Conflict (409): The request conflicts with the current state');
        expect(NetworkError::Forbidden->exception()->getMessage())
            ->toBe('Forbidden (403): Access denied to the requested resource');
        expect(NetworkError::Invalid->exception()->getMessage())
            ->toBe('Bad Request (400): The request is invalid');
        expect(NetworkError::Request->exception()->getMessage())
            ->toBe('Request failed: Unable to complete the HTTP request');
        expect(NetworkError::Server->exception()->getMessage())
            ->toBe('Internal Server Error (500): The server encountered an error');
        expect(NetworkError::Timeout->exception()->getMessage())
            ->toBe('Unprocessable Entity (422): The request could not be processed');
        expect(NetworkError::Unauthenticated->exception()->getMessage())
            ->toBe('Unauthorized (401): Authentication required');
        expect(NetworkError::UndefinedEndpoint->exception()->getMessage())
            ->toBe('Not Found (404): The requested endpoint does not exist');
        expect(NetworkError::Unexpected->exception()->getMessage())
            ->toBe('Unexpected response from the server');
    });

    it('provides default messages for all SerializationError variants', function (): void {
        expect(SerializationError::CouldNotAddItemsToCollection->exception()->getMessage())
            ->toBe('Could not add items to collection %className%');
        expect(SerializationError::EmptyCollection->exception()->getMessage())
            ->toBe('Collection cannot be empty');
        expect(SerializationError::InvalidItemType->exception()->getMessage())
            ->toBe('Invalid item type for %property% in %className%: expected %expected%, got %actual_type%');
        expect(SerializationError::MissingRequiredConstructorParameter->exception()->getMessage())
            ->toBe('Missing required constructor parameter "%paramName%" for class %className%');
        expect(SerializationError::Response->exception()->getMessage())
            ->toBe('Failed to serialize/deserialize response data');
        expect(SerializationError::UndefinedItemType->exception()->getMessage())
            ->toBe('Item type is not defined for %className%');
    });
});

// Alternative data-driven approach using dataset
dataset('error-messages', [
    // ClientError cases
    'client:authentication' => [ClientError::Authentication, 'Authentication error occurred'],
    'client:configuration' => [ClientError::Configuration, 'Configuration error detected'],
    'client:network' => [ClientError::Network, 'Network communication error'],
    'client:serialization' => [ClientError::Serialization, 'Data serialization error'],
    'client:validation' => [ClientError::Validation, 'Request validation failed'],

    // AuthenticationError cases
    'auth:token-expired' => [AuthenticationError::TokenExpired, 'Authentication token has expired'],
    'auth:token-invalid' => [AuthenticationError::TokenInvalid, 'Authentication token is invalid'],

    // ConfigurationError cases
    'config:http-client' => [ConfigurationError::HttpClientMissing, 'HTTP client is not configured'],
    'config:request-factory' => [ConfigurationError::HttpRequestFactoryMissing, 'HTTP request factory is not configured'],
    'config:response-factory' => [ConfigurationError::HttpResponseFactoryMissing, 'HTTP response factory is not configured'],
    'config:stream-factory' => [ConfigurationError::HttpStreamFactoryMissing, 'HTTP stream factory is not configured'],

    // NetworkError cases
    'network:conflict' => [NetworkError::Conflict, 'Conflict (409): The request conflicts with the current state'],
    'network:forbidden' => [NetworkError::Forbidden, 'Forbidden (403): Access denied to the requested resource'],
    'network:invalid' => [NetworkError::Invalid, 'Bad Request (400): The request is invalid'],
    'network:request' => [NetworkError::Request, 'Request failed: Unable to complete the HTTP request'],
    'network:server' => [NetworkError::Server, 'Internal Server Error (500): The server encountered an error'],
    'network:timeout' => [NetworkError::Timeout, 'Unprocessable Entity (422): The request could not be processed'],
    'network:unauthenticated' => [NetworkError::Unauthenticated, 'Unauthorized (401): Authentication required'],
    'network:undefined-endpoint' => [NetworkError::UndefinedEndpoint, 'Not Found (404): The requested endpoint does not exist'],
    'network:unexpected' => [NetworkError::Unexpected, 'Unexpected response from the server'],

    // SerializationError cases
    'serialization:could-not-add' => [SerializationError::CouldNotAddItemsToCollection, 'Could not add items to collection %className%'],
    'serialization:empty' => [SerializationError::EmptyCollection, 'Collection cannot be empty'],
    'serialization:invalid-type' => [SerializationError::InvalidItemType, 'Invalid item type for %property% in %className%: expected %expected%, got %actual_type%'],
    'serialization:missing-param' => [SerializationError::MissingRequiredConstructorParameter, 'Missing required constructor parameter "%paramName%" for class %className%'],
    'serialization:response' => [SerializationError::Response, 'Failed to serialize/deserialize response data'],
    'serialization:undefined-type' => [SerializationError::UndefinedItemType, 'Item type is not defined for %className%'],
]);

test('error enum provides expected default message', function (
    ClientError | AuthenticationError | ConfigurationError | NetworkError | SerializationError $error,
    string $expectedMessage,
): void {
    expect($error->exception()->getMessage())->toBe($expectedMessage);
})->with('error-messages');
