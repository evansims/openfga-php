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

it('throws the appropriate exception with error message based on HTTP status code', function (int $statusCode, string $exceptionClass, string $errorMessage): void {
    $response = new SimpleResponse($statusCode, $errorMessage);

    expect(fn () => DummyResponse::handleResponseException($response))
        ->toThrow($exceptionClass, $errorMessage);
})->with([
    [400, ApiValidationException::class, 'Validation error occurred'],
    [401, ApiUnauthenticatedException::class, 'Authentication required'],
    [403, ApiForbiddenException::class, 'Access forbidden'],
    [404, ApiEndpointException::class, 'Endpoint not found'],
    [409, ApiTransactionException::class, 'Transaction conflict'],
    [422, ApiTimeoutException::class, 'Request timed out'],
    [500, ApiInternalServerException::class, 'Internal server error'],
]);
