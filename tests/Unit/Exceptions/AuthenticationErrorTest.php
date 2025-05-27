<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{AuthenticationError, AuthenticationException, ClientThrowable};
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

/*
 * @param AuthenticationError $authenticationErrorCase
 */
it('AuthenticationError enum exception() factory creates AuthenticationException with all parameters', function (AuthenticationError $authenticationErrorCase): void {
    $mockRequest = new Request();
    $mockResponse = new Response();
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
it('AuthenticationError enum exception() factory creates AuthenticationException with default parameters', function (AuthenticationError $authenticationErrorCase): void {
    $exception = $authenticationErrorCase->exception();

    expect($exception)->toBeInstanceOf(AuthenticationException::class)
        ->and($exception)->toBeInstanceOf(ClientThrowable::class)
        ->and($exception->kind())->toBe($authenticationErrorCase)
        ->and($exception->request())->toBeNull()
        ->and($exception->response())->toBeNull()
        ->and($exception->context())->toBe([])
        ->and($exception->getPrevious())->toBeNull();
})->with(AuthenticationError::cases());
