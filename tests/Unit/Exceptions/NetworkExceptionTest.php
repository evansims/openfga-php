<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use Exception;
use OpenFGA\Exceptions\{ClientThrowable, DefaultMessages, NetworkError, NetworkException};
use OpenFGA\Translation\Translator;
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

describe('NetworkException', function (): void {
    test('constructs with all parameters', function (): void {
        $kind = NetworkError::Server;
        $request = new Request;
        $response = new Response;
        $context = ['url' => 'https://api.example.com', 'timeout' => 30];
        $previous = new RuntimeException('Connection failed');

        $exception = new NetworkException($kind, $request, $response, $context, $previous);

        expect($exception)->toBeInstanceOf(NetworkException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previous);
    });

    test('constructs with minimal parameters', function (): void {
        $kind = NetworkError::Timeout;

        $exception = new NetworkException($kind);

        expect($exception)->toBeInstanceOf(NetworkException::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    });

    test('uses custom message from context', function (): void {
        $customMessage = 'Custom network error message';
        $context = ['message' => $customMessage];

        $exception = new NetworkException(NetworkError::Request, context: $context);

        expect($exception->getMessage())->toBe($customMessage);
    });

    test('generates default message when no custom message provided', function (): void {
        $kind = NetworkError::Forbidden;
        $context = ['endpoint' => '/api/stores'];

        $exception = new NetworkException($kind, context: $context);

        $expectedMessage = Translator::trans(DefaultMessages::forNetworkError($kind), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('removes message from context parameters for translation', function (): void {
        $context = [
            'message' => '',  // Empty message should trigger default message generation
            'url' => 'https://api.example.com',
            'status' => 500,
        ];

        $exception = new NetworkException(NetworkError::Server, context: $context);

        // Should not include 'message' key in translation parameters
        $expectedContext = ['url' => 'https://api.example.com', 'status' => 500];
        $expectedMessage = Translator::trans(DefaultMessages::forNetworkError(NetworkError::Server), $expectedContext);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('implements ClientThrowable interface methods', function (): void {
        $kind = NetworkError::Invalid;
        $request = new Request;
        $response = new Response;
        $context = ['field' => 'value'];

        $exception = new NetworkException($kind, $request, $response, $context);

        expect($exception->kind())->toBe($kind);
        expect($exception->request())->toBe($request);
        expect($exception->response())->toBe($response);
        expect($exception->context())->toBe($context);
    });

    test('preserves exception chaining', function (): void {
        $originalException = new Exception('Original error');
        $networkException = new NetworkException(NetworkError::Unexpected, previous: $originalException);

        expect($networkException->getPrevious())->toBe($originalException);
    });

    test('handles all network error types', function (NetworkError $errorType): void {
        $exception = new NetworkException($errorType);

        expect($exception->kind())->toBe($errorType);
        expect($exception->getMessage())->toBeString()
            ->and($exception->getMessage())->not->toBeEmpty();
    })->with(NetworkError::cases());

    test('context data is preserved during construction', function (): void {
        $context = [
            'url' => 'https://api.openfga.example/stores/test',
            'method' => 'POST',
            'headers' => ['Authorization' => 'Bearer token'],
            'body_size' => 1024,
            'response_time' => 5.5,
        ];

        $exception = new NetworkException(NetworkError::Timeout, context: $context);

        expect($exception->context())->toBe($context);
    });

    test('exception code is always zero', function (): void {
        $exception = new NetworkException(NetworkError::Server);

        expect($exception->getCode())->toBe(0);
    });

    test('exception message handles translation parameters correctly', function (): void {
        $context = [
            'endpoint' => '/api/check',
            'method' => 'POST',
            'status_code' => 403,
        ];

        $exception = new NetworkException(NetworkError::Forbidden, context: $context);

        // Should include context parameters in the translated message
        $expectedMessage = Translator::trans(DefaultMessages::forNetworkError(NetworkError::Forbidden), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('works with different request and response types', function (): void {
        $request = new Request;
        $response = new Response;

        $exception = new NetworkException(
            NetworkError::Conflict,
            $request,
            $response,
            ['resource' => 'authorization_model', 'id' => 'model-123'],
        );

        expect($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context()['resource'])->toBe('authorization_model')
            ->and($exception->context()['id'])->toBe('model-123');
    });

    test('inherits from Exception correctly', function (): void {
        $exception = new NetworkException(NetworkError::UndefinedEndpoint);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect(is_subclass_of($exception, Exception::class))->toBeTrue();
    });

    test('can be caught as generic Exception', function (): void {
        $caught = false;
        $exception = new NetworkException(NetworkError::Request);

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
        $exception = new NetworkException(NetworkError::Unauthenticated);

        try {
            throw $exception;
        } catch (ClientThrowable $e) {
            $caught = true;
            expect($e)->toBe($exception);
        }

        expect($caught)->toBeTrue();
    });
});
