<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Models\{BatchCheckSingleResult, BatchCheckSingleResultInterface};
use OpenFGA\Responses\{BatchCheckResponse, BatchCheckResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\{RequestInterface};

describe('BatchCheckResponse', function (): void {
    beforeEach(function (): void {
        $this->validator = new SchemaValidator;
        $this->request = $this->createMock(RequestInterface::class);
    });

    test('implements BatchCheckResponseInterface', function (): void {
        $response = new BatchCheckResponse;

        expect($response)->toBeInstanceOf(BatchCheckResponseInterface::class);
    });

    test('constructs with empty result by default', function (): void {
        $response = new BatchCheckResponse;

        expect($response->getResult())->toBe([]);
    });

    test('constructs with provided result array', function (): void {
        $singleResult = new BatchCheckSingleResult(allowed: true);
        $result = ['correlation-1' => $singleResult];
        $response = new BatchCheckResponse(result: $result);

        expect($response->getResult())->toBe($result);
        expect($response->getResultForCorrelationId('correlation-1'))->toBe($singleResult);
    });

    test('getResultForCorrelationId returns null for missing correlation ID', function (): void {
        $response = new BatchCheckResponse;

        expect($response->getResultForCorrelationId('missing-id'))->toBeNull();
    });

    test('getResultForCorrelationId returns correct result for existing correlation ID', function (): void {
        $singleResult1 = new BatchCheckSingleResult(allowed: true);
        $singleResult2 = new BatchCheckSingleResult(allowed: false);

        $result = [
            'correlation-1' => $singleResult1,
            'correlation-2' => $singleResult2,
        ];

        $response = new BatchCheckResponse(result: $result);

        expect($response->getResultForCorrelationId('correlation-1'))->toBe($singleResult1);
        expect($response->getResultForCorrelationId('correlation-2'))->toBe($singleResult2);
        expect($response->getResultForCorrelationId('correlation-3'))->toBeNull();
    });

    test('has valid schema', function (): void {
        $schema = BatchCheckResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(BatchCheckResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);
        expect($properties['result']->getName())->toBe('result');
        expect($properties['result']->getType())->toBe('object');
        expect($properties['result']->isRequired())->toBeFalse();
    });

    test('fromResponse handles successful response with results', function (): void {
        $responseBody = [
            'result' => [
                'correlation-1' => [
                    'allowed' => true,
                    'request' => [
                        'user' => 'user:alice',
                        'relation' => 'reader',
                        'object' => 'document:test',
                    ],
                ],
                'correlation-2' => [
                    'allowed' => false,
                    'request' => [
                        'user' => 'user:bob',
                        'relation' => 'writer',
                        'object' => 'document:test',
                    ],
                ],
            ],
        ];

        $httpResponse = new SimpleResponse(
            statusCode: 200,
            body: json_encode($responseBody),
        );

        $response = BatchCheckResponse::fromResponse(
            response: $httpResponse,
            request: $this->request,
            validator: $this->validator,
        );

        expect($response)->toBeInstanceOf(BatchCheckResponse::class);

        $result = $response->getResult();
        expect($result)->toHaveCount(2);
        expect($result)->toHaveKey('correlation-1');
        expect($result)->toHaveKey('correlation-2');

        $result1 = $response->getResultForCorrelationId('correlation-1');
        expect($result1)->toBeInstanceOf(BatchCheckSingleResultInterface::class);
        expect($result1->getAllowed())->toBeTrue();

        $result2 = $response->getResultForCorrelationId('correlation-2');
        expect($result2)->toBeInstanceOf(BatchCheckSingleResultInterface::class);
        expect($result2->getAllowed())->toBeFalse();
    });

    test('fromResponse handles successful response with empty results', function (): void {
        $responseBody = ['result' => []];

        $httpResponse = new SimpleResponse(
            statusCode: 200,
            body: json_encode($responseBody),
        );

        $response = BatchCheckResponse::fromResponse(
            response: $httpResponse,
            request: $this->request,
            validator: $this->validator,
        );

        expect($response->getResult())->toBe([]);
    });

    test('fromResponse handles successful response without result key', function (): void {
        $responseBody = [];

        $httpResponse = new SimpleResponse(
            statusCode: 200,
            body: json_encode($responseBody),
        );

        $response = BatchCheckResponse::fromResponse(
            response: $httpResponse,
            request: $this->request,
            validator: $this->validator,
        );

        expect($response->getResult())->toBe([]);
    });

    test('fromResponse skips invalid result entries', function (): void {
        $responseBody = [
            'result' => [
                'correlation-1' => [
                    'allowed' => true,
                    'request' => [
                        'user' => 'user:alice',
                        'relation' => 'reader',
                        'object' => 'document:test',
                    ],
                ],
                // Invalid entries that should be skipped
                123 => ['allowed' => false], // Non-string correlation ID
                'correlation-2' => 'invalid-data', // Non-array result data
                'correlation-3' => null, // Null result data
            ],
        ];

        $httpResponse = new SimpleResponse(
            statusCode: 200,
            body: json_encode($responseBody),
        );

        $response = BatchCheckResponse::fromResponse(
            response: $httpResponse,
            request: $this->request,
            validator: $this->validator,
        );

        $result = $response->getResult();
        expect($result)->toHaveCount(1);
        expect($result)->toHaveKey('correlation-1');
        expect($result)->not->toHaveKey('123');
        expect($result)->not->toHaveKey('correlation-2');
        expect($result)->not->toHaveKey('correlation-3');
    });

    test('fromResponse handles response with non-array result', function (): void {
        $responseBody = ['result' => 'invalid'];

        $httpResponse = new SimpleResponse(
            statusCode: 200,
            body: json_encode($responseBody),
        );

        $response = BatchCheckResponse::fromResponse(
            response: $httpResponse,
            request: $this->request,
            validator: $this->validator,
        );

        expect($response->getResult())->toBe([]);
    });

    test('schema is cached', function (): void {
        $schema1 = BatchCheckResponse::schema();
        $schema2 = BatchCheckResponse::schema();

        expect($schema1)->toBe($schema2);
    });
});
