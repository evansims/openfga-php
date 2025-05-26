<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Exceptions\ClientException;
use OpenFGA\Exceptions\ClientThrowable;
use PsrMock\Psr7\{Request, Response};
use RuntimeException;
use Throwable;

/**
 * @param ClientError $clientErrorCase
 */
it('ClientError enum exception() factory creates ClientException with all parameters', function (ClientError $clientErrorCase): void {
    $mockRequest = new Request();
    $mockResponse = new Response();
    $context = ['detail' => 'some additional detail', 'code' => 123];
    $previousThrowable = new RuntimeException('Previous error');

    $exception = $clientErrorCase->exception($mockRequest, $mockResponse, $context, $previousThrowable);

    expect($exception)->toBeInstanceOf(ClientException::class)
        ->and($exception)->toBeInstanceOf(ClientThrowable::class)
        ->and($exception->kind())->toBe($clientErrorCase)
        ->and($exception->request())->toBe($mockRequest)
        ->and($exception->response())->toBe($mockResponse)
        ->and($exception->context())->toBe($context)
        ->and($exception->getPrevious())->toBe($previousThrowable);
})->with(ClientError::cases());

/**
 * @param ClientError $clientErrorCase
 */
it('ClientError enum exception() factory creates ClientException with default parameters', function (ClientError $clientErrorCase): void {
    $exception = $clientErrorCase->exception();

    expect($exception)->toBeInstanceOf(ClientException::class)
        ->and($exception)->toBeInstanceOf(ClientThrowable::class)
        ->and($exception->kind())->toBe($clientErrorCase)
        ->and($exception->request())->toBeNull()
        ->and($exception->response())->toBeNull()
        ->and($exception->context())->toBe([])
        ->and($exception->getPrevious())->toBeNull();
})->with(ClientError::cases());
