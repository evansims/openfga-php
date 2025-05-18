<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\{
    ApiEndpointException,
    ApiForbiddenException,
    ApiInternalServerException,
    ApiTimeoutException,
    ApiTransactionException,
    ApiUnauthenticatedException,
    ApiValidationException
};
use OpenFGA\Tests\Support\Responses\{DummyResponse, SimpleResponse};

it('throws the appropriate exception based on HTTP status code', function (int $statusCode, string $exceptionClass): void {
    $response = new SimpleResponse($statusCode, 'error');

    expect(fn () => DummyResponse::handleResponseException($response))
        ->toThrow($exceptionClass);
})->with([
    [400, ApiValidationException::class],
    [401, ApiUnauthenticatedException::class],
    [403, ApiForbiddenException::class],
    [404, ApiEndpointException::class],
    [409, ApiTransactionException::class],
    [422, ApiTimeoutException::class],
    [500, ApiInternalServerException::class],
]);

it('includes the error message from the response body', function (): void {
    $errorMessage = 'Custom error message';
    $response = new SimpleResponse(400, $errorMessage);

    expect(fn () => DummyResponse::handleResponseException($response))
        ->toThrow(ApiValidationException::class, $errorMessage);
});
