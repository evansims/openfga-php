<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{ClientThrowable, ConfigurationError, ConfigurationException};
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

describe('ConfigurationError', function (): void {
    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory creates ConfigurationException with all parameters', function (ConfigurationError $configurationErrorCase): void {
        $mockRequest = new Request;
        $mockResponse = new Response;
        $context = ['detail' => 'some additional detail', 'code' => 123];
        $previousThrowable = new RuntimeException('Previous error');

        $exception = $configurationErrorCase->exception($mockRequest, $mockResponse, $context, $previousThrowable);

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBe($mockRequest)
            ->and($exception->response())->toBe($mockResponse)
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previousThrowable);
    })->with(ConfigurationError::cases());

    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory creates ConfigurationException with default parameters', function (ConfigurationError $configurationErrorCase): void {
        $exception = $configurationErrorCase->exception();

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    })->with(ConfigurationError::cases());

    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory handles edge case with empty context array', function (ConfigurationError $configurationErrorCase): void {
        $mockRequest = new Request;
        $mockResponse = new Response;
        $emptyContext = [];

        $exception = $configurationErrorCase->exception($mockRequest, $mockResponse, $emptyContext);

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBe($mockRequest)
            ->and($exception->response())->toBe($mockResponse)
            ->and($exception->context())->toBe($emptyContext)
            ->and($exception->getPrevious())->toBeNull();
    })->with(ConfigurationError::cases());

    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory handles edge case with non-empty context and previous exception', function (ConfigurationError $configurationErrorCase): void {
        $context = [
            'message' => 'Configuration error occurred',
            'component' => 'http_client',
            'details' => ['factory' => 'missing', 'required' => true],
            'timestamp' => '2023-01-01T00:00:00Z',
        ];
        $previousThrowable = new RuntimeException('Underlying configuration issue');

        $exception = $configurationErrorCase->exception(null, null, $context, $previousThrowable);

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previousThrowable)
            ->and($exception->getMessage())->toBe('Configuration error occurred'); // Should use message from context
    })->with(ConfigurationError::cases());

    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory handles edge case with only request parameter', function (ConfigurationError $configurationErrorCase): void {
        $mockRequest = new Request;

        $exception = $configurationErrorCase->exception($mockRequest);

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBe($mockRequest)
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    })->with(ConfigurationError::cases());

    /*
     * @param ConfigurationError $configurationErrorCase
     */
    test('ConfigurationError enum exception() factory handles edge case with only response parameter', function (ConfigurationError $configurationErrorCase): void {
        $mockResponse = new Response;

        $exception = $configurationErrorCase->exception(null, $mockResponse);

        expect($exception)->toBeInstanceOf(ConfigurationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($configurationErrorCase)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBe($mockResponse)
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    })->with(ConfigurationError::cases());

    describe('getRequiredPsrInterface()', function (): void {
        test('HttpClientMissing returns correct PSR interface', function (): void {
            expect(ConfigurationError::HttpClientMissing->getRequiredPsrInterface())
                ->toBe('Psr\\Http\\Client\\ClientInterface');
        });

        test('HttpRequestFactoryMissing returns correct PSR interface', function (): void {
            expect(ConfigurationError::HttpRequestFactoryMissing->getRequiredPsrInterface())
                ->toBe('Psr\\Http\\Message\\RequestFactoryInterface');
        });

        test('HttpResponseFactoryMissing returns correct PSR interface', function (): void {
            expect(ConfigurationError::HttpResponseFactoryMissing->getRequiredPsrInterface())
                ->toBe('Psr\\Http\\Message\\ResponseFactoryInterface');
        });

        test('HttpStreamFactoryMissing returns correct PSR interface', function (): void {
            expect(ConfigurationError::HttpStreamFactoryMissing->getRequiredPsrInterface())
                ->toBe('Psr\\Http\\Message\\StreamFactoryInterface');
        });
    });

    describe('isHttpComponentMissing()', function (): void {
        test('HTTP component errors are correctly identified', function (): void {
            expect(ConfigurationError::HttpClientMissing->isHttpComponentMissing())->toBeTrue();
            expect(ConfigurationError::HttpRequestFactoryMissing->isHttpComponentMissing())->toBeTrue();
            expect(ConfigurationError::HttpResponseFactoryMissing->isHttpComponentMissing())->toBeTrue();
            expect(ConfigurationError::HttpStreamFactoryMissing->isHttpComponentMissing())->toBeTrue();
        });

        test('non-HTTP component errors are correctly identified', function (): void {
            expect(ConfigurationError::InvalidUrl->isHttpComponentMissing())->toBeFalse();
            expect(ConfigurationError::InvalidLanguage->isHttpComponentMissing())->toBeFalse();
            expect(ConfigurationError::InvalidRetryCount->isHttpComponentMissing())->toBeFalse();
        });
    });
});
