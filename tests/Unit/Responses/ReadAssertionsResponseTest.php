<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\Assertions;
use OpenFGA\Responses\{ReadAssertionsResponse, ReadAssertionsResponseInterface};
use OpenFGA\Schemas\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('ReadAssertionsResponse', function (): void {
    test('implements ReadAssertionsResponseInterface', function (): void {
        $assertions = new Assertions([]);
        $response = new ReadAssertionsResponse($assertions, 'model-123');

        expect($response)->toBeInstanceOf(ReadAssertionsResponseInterface::class);
    });

    test('constructs with assertions and model', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:alice',
            relation: 'viewer',
            object: 'document:readme',
        );
        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: true,
        );
        $assertions = new Assertions([$assertion]);
        $model = 'model-123';

        $response = new ReadAssertionsResponse($assertions, $model);

        expect($response->getAssertions())->toBe($assertions);
        expect($response->getModel())->toBe($model);
    });

    test('constructs with null assertions', function (): void {
        $model = 'model-123';
        $response = new ReadAssertionsResponse(null, $model);

        expect($response->getAssertions())->toBeNull();
        expect($response->getModel())->toBe($model);
    });

    test('constructs with empty assertions collection', function (): void {
        $assertions = new Assertions([]);
        $model = 'model-456';
        $response = new ReadAssertionsResponse($assertions, $model);

        expect($response->getAssertions())->toHaveCount(0);
        expect($response->getModel())->toBe($model);
    });

    test('handles single assertion', function (): void {
        $tupleKey = new AssertionTupleKey(
            user: 'user:bob',
            relation: 'owner',
            object: 'document:secret',
        );
        $assertion = new Assertion(
            tupleKey: $tupleKey,
            expectation: false,
        );
        $assertions = new Assertions([$assertion]);
        $response = new ReadAssertionsResponse($assertions, 'model-789');

        expect($response->getAssertions())->toHaveCount(1);
        expect($response->getAssertions()->first())->toBe($assertion);
    });

    test('handles multiple assertions', function (): void {
        $tupleKey1 = new AssertionTupleKey(
            user: 'user:alice',
            relation: 'viewer',
            object: 'document:readme',
        );
        $tupleKey2 = new AssertionTupleKey(
            user: 'user:bob',
            relation: 'editor',
            object: 'document:readme',
        );
        $assertion1 = new Assertion(tupleKey: $tupleKey1, expectation: true);
        $assertion2 = new Assertion(tupleKey: $tupleKey2, expectation: false);
        $assertions = new Assertions([$assertion1, $assertion2]);

        $response = new ReadAssertionsResponse($assertions, 'model-abc');

        expect($response->getAssertions())->toHaveCount(2);
        expect($response->getAssertions()->toArray())->toBe([$assertion1, $assertion2]);
    });

    test('schema returns correct structure', function (): void {
        $schema = ReadAssertionsResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ReadAssertionsResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);
        expect($properties)->toHaveKeys(['assertions', 'authorization_model_id']);

        expect($properties['assertions']->name)->toBe('assertions');
        expect($properties['assertions']->type)->toBe('object');
        expect($properties['assertions']->required)->toBeFalse();

        expect($properties['authorization_model_id']->name)->toBe('authorization_model_id');
        expect($properties['authorization_model_id']->type)->toBe('string');
        expect($properties['authorization_model_id']->required)->toBeTrue();
    });

    test('schema is cached', function (): void {
        $schema1 = ReadAssertionsResponse::schema();
        $schema2 = ReadAssertionsResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    // Note: fromResponse method testing would require integration tests due to SchemaValidator complexity
    // These tests focus on the model's direct functionality

    test('handles response data with null assertions', function (): void {
        $response = new ReadAssertionsResponse(null, 'model-456');

        expect($response)->toBeInstanceOf(ReadAssertionsResponseInterface::class);
        expect($response->getModel())->toBe('model-456');
        expect($response->getAssertions())->toBeNull();
    });

    test('handles empty assertions array data', function (): void {
        $assertions = new Assertions([]);
        $response = new ReadAssertionsResponse($assertions, 'model-789');

        expect($response)->toBeInstanceOf(ReadAssertionsResponseInterface::class);
        expect($response->getModel())->toBe('model-789');
        expect($response->getAssertions())->toHaveCount(0);
    });

    // Removed fromResponse error handling test - handled in integration tests

    // Removed fromResponse validation error test - handled in integration tests

    // Removed fromResponse missing field test - handled in integration tests

    test('handles UUID format model IDs', function (): void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $assertions = new Assertions([]);
        $response = new ReadAssertionsResponse($assertions, $uuid);

        expect($response->getModel())->toBe($uuid);
    });

    test('handles large assertion collections', function (): void {
        $assertions = [];

        for ($i = 0; 50 > $i; ++$i) {
            $tupleKey = new AssertionTupleKey(
                user: "user:user{$i}",
                relation: 'viewer',
                object: "document:doc{$i}",
            );
            $assertions[] = new Assertion(
                tupleKey: $tupleKey,
                expectation: 0 === $i % 2,
            );
        }
        $assertionsCollection = new Assertions($assertions);
        $response = new ReadAssertionsResponse($assertionsCollection, 'model-large');

        expect($response->getAssertions())->toHaveCount(50);
        expect($response->getModel())->toBe('model-large');
    });

    test('handles complex assertion expectations', function (): void {
        $tupleKey1 = new AssertionTupleKey(
            user: 'team:engineering#member',
            relation: 'can_edit',
            object: 'repository:backend#main',
        );
        $tupleKey2 = new AssertionTupleKey(
            user: 'user:alice',
            relation: 'owner',
            object: 'organization:acme',
        );
        $assertion1 = new Assertion(tupleKey: $tupleKey1, expectation: true);
        $assertion2 = new Assertion(tupleKey: $tupleKey2, expectation: false);
        $assertions = new Assertions([$assertion1, $assertion2]);

        $response = new ReadAssertionsResponse($assertions, 'model-complex');

        expect($response->getAssertions())->toHaveCount(2);
        expect($response->getAssertions()->first()->getExpectation())->toBeTrue();
        expect($response->getAssertions()->toArray()[1]->getExpectation())->toBeFalse();
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 401 unauthorized error', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Unauthorized']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Internal server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    describe('fromResponse successful scenarios', function (): void {
        test('successfully processes valid response data', function (): void {
            $responseData = [
                'assertions' => [
                    [
                        'tuple_key' => [
                            'user' => 'user:alice',
                            'relation' => 'reader',
                            'object' => 'document:budget',
                        ],
                        'expectation' => true,
                    ],
                ],
                'authorization_model_id' => 'model-123',
            ];

            $httpResponse = new SimpleResponse(200, json_encode($responseData));
            $request = test()->createMock(RequestInterface::class);
            $validator = new SchemaValidator;

            $result = ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);

            expect($result)->toBeInstanceOf(ReadAssertionsResponse::class);
            expect($result->getModel())->toBe('model-123');
            expect($result->getAssertions())->not->toBeNull();
            expect($result->getAssertions()->count())->toBe(1);
        });

        test('handles response with no assertions', function (): void {
            $responseData = [
                'authorization_model_id' => 'model-456',
            ];

            $httpResponse = new SimpleResponse(200, json_encode($responseData));
            $request = test()->createMock(RequestInterface::class);
            $validator = new SchemaValidator;

            $result = ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);

            expect($result)->toBeInstanceOf(ReadAssertionsResponse::class);
            expect($result->getModel())->toBe('model-456');
            expect($result->getAssertions())->toBeNull();
        });

        test('handles response with empty assertions array', function (): void {
            $responseData = [
                'assertions' => [],
                'authorization_model_id' => 'model-789',
            ];

            $httpResponse = new SimpleResponse(200, json_encode($responseData));
            $request = test()->createMock(RequestInterface::class);
            $validator = new SchemaValidator;

            $result = ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);

            expect($result)->toBeInstanceOf(ReadAssertionsResponse::class);
            expect($result->getModel())->toBe('model-789');
            expect($result->getAssertions())->not->toBeNull();
            expect($result->getAssertions()->count())->toBe(0);
        });

        test('handles response with multiple assertions', function (): void {
            $responseData = [
                'assertions' => [
                    [
                        'tuple_key' => [
                            'user' => 'user:alice',
                            'relation' => 'reader',
                            'object' => 'document:budget',
                        ],
                        'expectation' => true,
                    ],
                    [
                        'tuple_key' => [
                            'user' => 'user:bob',
                            'relation' => 'writer',
                            'object' => 'document:budget',
                        ],
                        'expectation' => false,
                    ],
                ],
                'authorization_model_id' => 'model-abc',
            ];

            $httpResponse = new SimpleResponse(200, json_encode($responseData));
            $request = test()->createMock(RequestInterface::class);
            $validator = new SchemaValidator;

            $result = ReadAssertionsResponse::fromResponse($httpResponse, $request, $validator);

            expect($result)->toBeInstanceOf(ReadAssertionsResponse::class);
            expect($result->getModel())->toBe('model-abc');
            expect($result->getAssertions())->not->toBeNull();
            expect($result->getAssertions()->count())->toBe(2);

            $assertions = $result->getAssertions()->toArray();
            expect($assertions[0]->getTupleKey()->getUser())->toBe('user:alice');
            expect($assertions[0]->getExpectation())->toBeTrue();
            expect($assertions[1]->getTupleKey()->getUser())->toBe('user:bob');
            expect($assertions[1]->getExpectation())->toBeFalse();
        });
    });
});
