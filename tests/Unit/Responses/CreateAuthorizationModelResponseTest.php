<?php

declare(strict_types=1);

use OpenFGA\Responses\{CreateAuthorizationModelResponse, CreateAuthorizationModelResponseInterface};

test('CreateAuthorizationModelResponse implements CreateAuthorizationModelResponseInterface', function (): void {
    $response = new CreateAuthorizationModelResponse('model-id-123');
    expect($response)->toBeInstanceOf(CreateAuthorizationModelResponseInterface::class);
});

test('CreateAuthorizationModelResponse constructs and returns model', function (): void {
    $response = new CreateAuthorizationModelResponse('model-id-xyz-456');
    expect($response->getModel())->toBe('model-id-xyz-456');
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('CreateAuthorizationModelResponse schema returns expected structure', function (): void {
    $schema = CreateAuthorizationModelResponse::schema();

    expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
    expect($schema->getClassName())->toBe(CreateAuthorizationModelResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(1);
    expect($properties['authorization_model_id']->name)->toBe('authorization_model_id');
    expect($properties['authorization_model_id']->type)->toBe('string');
    expect($properties['authorization_model_id']->required)->toBeTrue();
});

test('CreateAuthorizationModelResponse schema is cached', function (): void {
    $schema1 = CreateAuthorizationModelResponse::schema();
    $schema2 = CreateAuthorizationModelResponse::schema();

    expect($schema1)->toBe($schema2);
});

test('CreateAuthorizationModelResponse handles empty model ID', function (): void {
    $response = new CreateAuthorizationModelResponse('');
    expect($response->getModel())->toBe('');
});

test('CreateAuthorizationModelResponse handles UUID format model ID', function (): void {
    $uuid = '550e8400-e29b-41d4-a716-446655440000';
    $response = new CreateAuthorizationModelResponse($uuid);
    expect($response->getModel())->toBe($uuid);
});

test('CreateAuthorizationModelResponse preserves exact model ID format', function (): void {
    $modelId = '  model-with-spaces  ';
    $response = new CreateAuthorizationModelResponse($modelId);
    expect($response->getModel())->toBe($modelId);
});
