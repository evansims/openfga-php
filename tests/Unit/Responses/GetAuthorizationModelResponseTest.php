<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, TypeDefinition};
use OpenFGA\Models\Collections\TypeDefinitions;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Responses\{GetAuthorizationModelResponse, GetAuthorizationModelResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('GetAuthorizationModelResponse', function (): void {
    test('implements GetAuthorizationModelResponseInterface', function (): void {
        $response = new GetAuthorizationModelResponse;
        expect($response)->toBeInstanceOf(GetAuthorizationModelResponseInterface::class);
    });

    test('constructs with null model', function (): void {
        $response = new GetAuthorizationModelResponse;
        expect($response->getModel())->toBeNull();
    });

    test('constructs with AuthorizationModel', function (): void {
        $typeDefinitions = new TypeDefinitions(
            new TypeDefinition('user'),
            new TypeDefinition('document'),
        );

        $model = new AuthorizationModel(
            id: 'model-123',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        $response = new GetAuthorizationModelResponse($model);
        expect($response->getModel())->toBe($model);
    });

    test('schema returns expected structure', function (): void {
        $schema = GetAuthorizationModelResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(GetAuthorizationModelResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);
        expect($properties['authorization_model']->name)->toBe('authorization_model');
        expect($properties['authorization_model']->type)->toBe('object');
        expect($properties['authorization_model']->required)->toBeFalse();
    });

    test('schema is cached', function (): void {
        $schema1 = GetAuthorizationModelResponse::schema();
        $schema2 = GetAuthorizationModelResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles model with conditions', function (): void {
        $model = $this->createMock(AuthorizationModelInterface::class);

        $response = new GetAuthorizationModelResponse($model);

        expect($response->getModel())->toBe($model);
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 404 not found', function (): void {
        $httpResponse = new SimpleResponse(404, json_encode(['code' => 'authorization_model_not_found', 'message' => 'Authorization model not found']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);
});
