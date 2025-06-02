<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use Exception;
use OpenFGA\Exceptions\{ClientError, ClientException, ClientThrowable, DefaultMessages};
use OpenFGA\Translation\Translator;
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

describe('ClientException', function (): void {
    test('constructs with all parameters', function (): void {
        $kind = ClientError::Authentication;
        $request = new Request;
        $response = new Response;
        $context = ['token_type' => 'Bearer', 'endpoint' => '/api/check'];
        $previous = new RuntimeException('Authentication failed');

        $exception = new ClientException($kind, $request, $response, $context, $previous);

        expect($exception)->toBeInstanceOf(ClientException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previous);
    });

    test('constructs with minimal parameters', function (): void {
        $kind = ClientError::Configuration;

        $exception = new ClientException($kind);

        expect($exception)->toBeInstanceOf(ClientException::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    });

    test('uses custom message from context', function (): void {
        $customMessage = 'Custom client error message';
        $context = ['message' => $customMessage];

        $exception = new ClientException(ClientError::Network, context: $context);

        expect($exception->getMessage())->toBe($customMessage);
    });

    test('generates default message when no custom message provided', function (): void {
        $kind = ClientError::Validation;
        $context = ['field' => 'store_id', 'value' => 'invalid-id'];

        $exception = new ClientException($kind, context: $context);

        $expectedMessage = Translator::trans(DefaultMessages::forClientError($kind), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('removes message from context parameters for translation', function (): void {
        $context = [
            'message' => '',  // Empty message should trigger default message generation
            'config_key' => 'api_url',
            'expected_format' => 'valid URL',
        ];

        $exception = new ClientException(ClientError::Configuration, context: $context);

        // Should not include 'message' key in translation parameters
        $expectedContext = ['config_key' => 'api_url', 'expected_format' => 'valid URL'];
        $expectedMessage = Translator::trans(DefaultMessages::forClientError(ClientError::Configuration), $expectedContext);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('implements ClientThrowable interface methods', function (): void {
        $kind = ClientError::Serialization;
        $request = new Request;
        $response = new Response;
        $context = ['data_type' => 'AuthorizationModel'];

        $exception = new ClientException($kind, $request, $response, $context);

        expect($exception->kind())->toBe($kind);
        expect($exception->request())->toBe($request);
        expect($exception->response())->toBe($response);
        expect($exception->context())->toBe($context);
    });

    test('preserves exception chaining', function (): void {
        $originalException = new Exception('Configuration parsing error');
        $clientException = new ClientException(ClientError::Configuration, previous: $originalException);

        expect($clientException->getPrevious())->toBe($originalException);
    });

    test('handles all client error types', function (ClientError $errorType): void {
        $exception = new ClientException($errorType);

        expect($exception->kind())->toBe($errorType);
        expect($exception->getMessage())->toBeString()
            ->and($exception->getMessage())->not->toBeEmpty();
    })->with(ClientError::cases());

    test('context data is preserved during construction', function (): void {
        $context = [
            'store_id' => 'store-123',
            'operation' => 'check',
            'user' => 'user:anne',
            'object' => 'document:budget',
            'relation' => 'viewer',
            'request_id' => 'req-456',
        ];

        $exception = new ClientException(ClientError::Validation, context: $context);

        expect($exception->context())->toBe($context);
    });

    test('exception code is always zero', function (): void {
        $exception = new ClientException(ClientError::Network);

        expect($exception->getCode())->toBe(0);
    });

    test('exception message handles translation parameters correctly', function (): void {
        $context = [
            'api_endpoint' => 'https://api.openfga.example',
            'timeout_seconds' => 30,
            'retry_count' => 3,
        ];

        $exception = new ClientException(ClientError::Network, context: $context);

        // Should include context parameters in the translated message
        $expectedMessage = Translator::trans(DefaultMessages::forClientError(ClientError::Network), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('works with different request and response types', function (): void {
        $request = new Request;
        $response = new Response;

        $exception = new ClientException(
            ClientError::Authentication,
            $request,
            $response,
            ['auth_type' => 'client_credentials', 'client_id' => 'app-123'],
        );

        expect($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context()['auth_type'])->toBe('client_credentials')
            ->and($exception->context()['client_id'])->toBe('app-123');
    });

    test('inherits from Exception correctly', function (): void {
        $exception = new ClientException(ClientError::Validation);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect(is_subclass_of($exception, Exception::class))->toBeTrue();
    });

    test('can be caught as generic Exception', function (): void {
        $caught = false;
        $exception = new ClientException(ClientError::Configuration);

        try {
            throw $exception;
        } catch (Exception $e) {
            $caught = true;
            expect($e)->toBe($exception);
        }

        expect($caught)->toBeTrue();
    });

    test('can be caught as ClientThrowable', function (): void {
        $caught = false;
        $exception = new ClientException(ClientError::Serialization);

        try {
            throw $exception;
        } catch (ClientThrowable $e) {
            $caught = true;
            expect($e)->toBe($exception);
        }

        expect($caught)->toBeTrue();
    });

    test('handles authentication errors with token information', function (): void {
        $context = [
            'auth_method' => 'client_credentials',
            'token_endpoint' => 'https://auth.example.com/oauth/token',
            'client_id' => 'test-client',
            'scopes' => ['read', 'write'],
        ];

        $exception = new ClientException(ClientError::Authentication, context: $context);

        expect($exception->context()['auth_method'])->toBe('client_credentials');
        expect($exception->context()['token_endpoint'])->toBe('https://auth.example.com/oauth/token');
        expect($exception->context()['scopes'])->toBe(['read', 'write']);
    });

    test('handles configuration errors with setup details', function (): void {
        $context = [
            'config_file' => '.env',
            'missing_keys' => ['FGA_API_URL', 'FGA_STORE_ID'],
            'provided_keys' => ['FGA_CLIENT_ID'],
            'suggestion' => 'Set the missing environment variables',
        ];

        $exception = new ClientException(ClientError::Configuration, context: $context);

        expect($exception->kind())->toBe(ClientError::Configuration);
        expect($exception->context()['missing_keys'])->toBe(['FGA_API_URL', 'FGA_STORE_ID']);
        expect($exception->context()['suggestion'])->toBe('Set the missing environment variables');
    });

    test('handles network errors with connection details', function (): void {
        $context = [
            'host' => 'api.openfga.example',
            'port' => 443,
            'protocol' => 'https',
            'connection_timeout' => 10,
            'read_timeout' => 30,
            'error_type' => 'connection_refused',
        ];

        $exception = new ClientException(ClientError::Network, context: $context);

        expect($exception->kind())->toBe(ClientError::Network);
        expect($exception->context()['host'])->toBe('api.openfga.example');
        expect($exception->context()['error_type'])->toBe('connection_refused');
    });

    test('handles validation errors with field-specific information', function (): void {
        $context = [
            'operation' => 'check',
            'invalid_fields' => ['user', 'relation'],
            'validation_rules' => [
                'user' => 'must follow user:id format',
                'relation' => 'must be a valid relation name',
            ],
            'provided_values' => [
                'user' => 'invalid-user-format',
                'relation' => 'invalid relation',
            ],
        ];

        $exception = new ClientException(ClientError::Validation, context: $context);

        expect($exception->kind())->toBe(ClientError::Validation);
        expect($exception->context()['operation'])->toBe('check');
        expect($exception->context()['invalid_fields'])->toBe(['user', 'relation']);
    });

    test('handles serialization errors with data context', function (): void {
        $context = [
            'operation' => 'write_tuples',
            'serialization_stage' => 'request_encoding',
            'data_type' => 'TupleKeys',
            'problematic_data' => ['user' => null, 'relation' => '', 'object' => 'doc:1'],
        ];

        $exception = new ClientException(ClientError::Serialization, context: $context);

        expect($exception->kind())->toBe(ClientError::Serialization);
        expect($exception->context()['serialization_stage'])->toBe('request_encoding');
        expect($exception->context()['data_type'])->toBe('TupleKeys');
    });
});
