<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{AuthenticationError, AuthenticationException, ClientThrowable};
use OpenFGA\Messages;
use OpenFGA\Translation\Translator;
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

describe('AuthenticationError', function (): void {
    /*
     * @param AuthenticationError $authenticationErrorCase
     */
    test('AuthenticationError enum exception() factory creates AuthenticationException with all parameters', function (AuthenticationError $authenticationErrorCase): void {
        $mockRequest = new Request;
        $mockResponse = new Response;
        $context = ['detail' => 'some additional detail', 'code' => 123];
        $previousThrowable = new RuntimeException('Previous error');

        $exception = $authenticationErrorCase->exception($mockRequest, $mockResponse, $context, $previousThrowable);

        expect($exception)->toBeInstanceOf(AuthenticationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($authenticationErrorCase)
            ->and($exception->request())->toBe($mockRequest)
            ->and($exception->response())->toBe($mockResponse)
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previousThrowable);
    })->with(AuthenticationError::cases());

    /*
     * @param AuthenticationError $authenticationErrorCase
     */
    test('AuthenticationError enum exception() factory creates AuthenticationException with default parameters', function (AuthenticationError $authenticationErrorCase): void {
        $exception = $authenticationErrorCase->exception();

        expect($exception)->toBeInstanceOf(AuthenticationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($authenticationErrorCase)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    })->with(AuthenticationError::cases());

    test('getUserMessage returns correct message for TokenExpired', function (): void {
        $error = AuthenticationError::TokenExpired;
        $message = $error->getUserMessage();

        expect($message)->toBeString()
            ->and($message)->toBe(Translator::trans(Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED));
    });

    test('getUserMessage returns correct message for TokenInvalid', function (): void {
        $error = AuthenticationError::TokenInvalid;
        $message = $error->getUserMessage();

        expect($message)->toBeString()
            ->and($message)->toBe(Translator::trans(Messages::AUTH_USER_MESSAGE_TOKEN_INVALID));
    });

    test('getUserMessage with custom locale', function (): void {
        $error = AuthenticationError::TokenExpired;
        $message = $error->getUserMessage('es');

        expect($message)->toBeString()
            ->and($message)->toBe(Translator::trans(Messages::AUTH_USER_MESSAGE_TOKEN_EXPIRED, [], 'es'));
    });

    test('isTokenRefreshable returns true for TokenExpired', function (): void {
        $error = AuthenticationError::TokenExpired;

        expect($error->isTokenRefreshable())->toBeTrue();
    });

    test('isTokenRefreshable returns false for TokenInvalid', function (): void {
        $error = AuthenticationError::TokenInvalid;

        expect($error->isTokenRefreshable())->toBeFalse();
    });

    test('enum cases have correct string values', function (): void {
        expect(AuthenticationError::TokenExpired->value)->toBe('token_expired');
        expect(AuthenticationError::TokenInvalid->value)->toBe('token_invalid');
    });

    test('exception captures correct location information', function (): void {
        $error = AuthenticationError::TokenExpired;

        // Create exception and verify it has location info
        $exception = $error->exception();

        expect($exception->getFile())->toBeString()
            ->and($exception->getLine())->toBeInt()
            ->and($exception->getLine())->toBeGreaterThan(0);
    });

    test('exception with context maintains context data', function (): void {
        $error = AuthenticationError::TokenInvalid;
        $context = [
            'token_hint' => 'abc...xyz',
            'expires_at' => '2024-01-01T00:00:00Z',
            'client_id' => 'test-client',
        ];

        $exception = $error->exception(context: $context);

        expect($exception->context())->toBe($context);
    });

    test('exception chaining preserves previous exception', function (): void {
        $error = AuthenticationError::TokenExpired;
        $previous = new RuntimeException('JWT decode failed');

        $exception = $error->exception(prev: $previous);

        expect($exception->getPrevious())->toBe($previous);
    });

    test('all enum cases are covered by tests', function (): void {
        $allCases = AuthenticationError::cases();

        expect($allCases)->toHaveCount(2)
            ->and($allCases)->toContain(AuthenticationError::TokenExpired)
            ->and($allCases)->toContain(AuthenticationError::TokenInvalid);
    });

    test('getUserMessage works with all enum cases', function (AuthenticationError $case): void {
        $message = $case->getUserMessage();

        expect($message)->toBeString()
            ->and($message)->not->toBeEmpty();
    })->with(AuthenticationError::cases());

    test('isTokenRefreshable works with all enum cases', function (AuthenticationError $case): void {
        $refreshable = $case->isTokenRefreshable();

        expect($refreshable)->toBeBool();
    })->with(AuthenticationError::cases());
});
