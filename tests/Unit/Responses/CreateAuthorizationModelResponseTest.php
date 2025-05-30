<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Responses\{CreateAuthorizationModelResponse, CreateAuthorizationModelResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('CreateAuthorizationModelResponse', function (): void {
    test('implements CreateAuthorizationModelResponseInterface', function (): void {
        $response = new CreateAuthorizationModelResponse('model-id-123');
        expect($response)->toBeInstanceOf(CreateAuthorizationModelResponseInterface::class);
    });

    test('constructs and returns model', function (): void {
        $response = new CreateAuthorizationModelResponse('model-id-xyz-456');
        expect($response->getModel())->toBe('model-id-xyz-456');
    });

    test('schema returns expected structure', function (): void {
        $schema = CreateAuthorizationModelResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(CreateAuthorizationModelResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);
        expect($properties['authorization_model_id']->name)->toBe('authorization_model_id');
        expect($properties['authorization_model_id']->type)->toBe('string');
        expect($properties['authorization_model_id']->required)->toBeTrue();
    });

    test('schema is cached', function (): void {
        $schema1 = CreateAuthorizationModelResponse::schema();
        $schema2 = CreateAuthorizationModelResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty model ID', function (): void {
        $response = new CreateAuthorizationModelResponse('');
        expect($response->getModel())->toBe('');
    });

    test('handles UUID format model ID', function (): void {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $response = new CreateAuthorizationModelResponse($uuid);
        expect($response->getModel())->toBe($uuid);
    });

    test('preserves exact model ID format', function (): void {
        $modelId = '  model-with-spaces  ';
        $response = new CreateAuthorizationModelResponse($modelId);
        expect($response->getModel())->toBe($modelId);
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        CreateAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 401 unauthorized', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Invalid credentials']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        CreateAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        CreateAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);
});
