<?php

declare(strict_types=1);

use OpenFGA\Models\{AuthorizationModel, TypeDefinition};
use OpenFGA\Models\Collections\TypeDefinitions;
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Responses\{GetAuthorizationModelResponse, GetAuthorizationModelResponseInterface};
use OpenFGA\Schema\SchemaValidator;
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

test('GetAuthorizationModelResponse implements GetAuthorizationModelResponseInterface', function (): void {
    $response = new GetAuthorizationModelResponse();
    expect($response)->toBeInstanceOf(GetAuthorizationModelResponseInterface::class);
});

test('GetAuthorizationModelResponse constructs with null model', function (): void {
    $response = new GetAuthorizationModelResponse();
    expect($response->getModel())->toBeNull();
});

test('GetAuthorizationModelResponse constructs with AuthorizationModel', function (): void {
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

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('GetAuthorizationModelResponse schema returns expected structure', function (): void {
    $schema = GetAuthorizationModelResponse::schema();

    expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
    expect($schema->getClassName())->toBe(GetAuthorizationModelResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(1);
    expect($properties['authorization_model']->name)->toBe('authorization_model');
    expect($properties['authorization_model']->type)->toBe('object');
    expect($properties['authorization_model']->required)->toBeFalse();
});

test('GetAuthorizationModelResponse schema is cached', function (): void {
    $schema1 = GetAuthorizationModelResponse::schema();
    $schema2 = GetAuthorizationModelResponse::schema();

    expect($schema1)->toBe($schema2);
});

test('GetAuthorizationModelResponse handles model with conditions', function (): void {
    $model = $this->createMock(OpenFGA\Models\AuthorizationModelInterface::class);

    $response = new GetAuthorizationModelResponse($model);

    expect($response->getModel())->toBe($model);
});

test('fromResponse handles error responses with non-200 status', function (): void {
    $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
    $request = Mockery::mock(RequestInterface::class);
    $validator = new SchemaValidator();

    $this->expectException(OpenFGA\Exceptions\NetworkException::class);
    GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
});

test('fromResponse handles 404 not found', function (): void {
    $httpResponse = new SimpleResponse(404, json_encode(['code' => 'authorization_model_not_found', 'message' => 'Authorization model not found']));
    $request = Mockery::mock(RequestInterface::class);
    $validator = new SchemaValidator();

    $this->expectException(OpenFGA\Exceptions\NetworkException::class);
    GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
});

test('fromResponse handles 500 internal server error', function (): void {
    $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Server error']));
    $request = Mockery::mock(RequestInterface::class);
    $validator = new SchemaValidator();

    $this->expectException(OpenFGA\Exceptions\NetworkException::class);
    GetAuthorizationModelResponse::fromResponse($httpResponse, $request, $validator);
});
