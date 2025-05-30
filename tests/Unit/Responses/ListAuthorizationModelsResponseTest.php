<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Models\{AuthorizationModel, TypeDefinition};
use OpenFGA\Models\Collections\{AuthorizationModels, TypeDefinitions};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Responses\{ListAuthorizationModelsResponse, ListAuthorizationModelsResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

use function strlen;

describe('ListAuthorizationModelsResponse', function (): void {
    test('implements ListAuthorizationModelsResponseInterface', function (): void {
        $models = new AuthorizationModels();
        $response = new ListAuthorizationModelsResponse($models);
        expect($response)->toBeInstanceOf(ListAuthorizationModelsResponseInterface::class);
    });

    test('constructs with models only', function (): void {
        $typeDefinitions = new TypeDefinitions(
            new TypeDefinition('user'),
            new TypeDefinition('document'),
        );

        $model1 = new AuthorizationModel(
            id: 'model-1',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        $model2 = new AuthorizationModel(
            id: 'model-2',
            schemaVersion: SchemaVersion::V1_1,
            typeDefinitions: $typeDefinitions,
        );

        $models = new AuthorizationModels($model1, $model2);

        $response = new ListAuthorizationModelsResponse($models);

        expect($response->getModels())->toBe($models);
        expect($response->getContinuationToken())->toBeNull();
    });

    test('constructs with models and continuation token', function (): void {
        $models = new AuthorizationModels();
        $continuationToken = 'next-page-token-xyz';

        $response = new ListAuthorizationModelsResponse($models, $continuationToken);

        expect($response->getModels())->toBe($models);
        expect($response->getContinuationToken())->toBe($continuationToken);
    });

    test('handles empty models collection', function (): void {
        $models = new AuthorizationModels();
        $response = new ListAuthorizationModelsResponse($models);

        expect($response->getModels())->toBe($models);
        expect($response->getModels()->count())->toBe(0);
    });

    test('handles large models collection', function (): void {
        $models = new AuthorizationModels();

        for ($i = 1; $i <= 10; ++$i) {
            $typeDefinitions = new TypeDefinitions(
                new TypeDefinition('user'),
                new TypeDefinition("resource{$i}"),
            );

            $model = new AuthorizationModel(
                id: "model-{$i}",
                schemaVersion: SchemaVersion::V1_1,
                typeDefinitions: $typeDefinitions,
            );

            $models->add($model);
        }

        $response = new ListAuthorizationModelsResponse($models, 'pagination-token');

        expect($response->getModels()->count())->toBe(10);
        expect($response->getContinuationToken())->toBe('pagination-token');
    });

    test('schema returns expected structure', function (): void {
        $schema = ListAuthorizationModelsResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ListAuthorizationModelsResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        expect($properties)->toHaveKeys(['authorization_models', 'continuation_token']);

        expect($properties['authorization_models']->name)->toBe('authorization_models');
        expect($properties['authorization_models']->type)->toBe('object');
        expect($properties['authorization_models']->required)->toBeTrue();

        expect($properties['continuation_token']->name)->toBe('continuation_token');
        expect($properties['continuation_token']->type)->toBe('string');
        expect($properties['continuation_token']->required)->toBeFalse();
    });

    test('schema is cached', function (): void {
        $schema1 = ListAuthorizationModelsResponse::schema();
        $schema2 = ListAuthorizationModelsResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty continuation token', function (): void {
        $models = new AuthorizationModels();
        $response = new ListAuthorizationModelsResponse($models, '');

        expect($response->getContinuationToken())->toBe('');
    });

    test('handles long continuation token', function (): void {
        $models = new AuthorizationModels();
        $longToken = str_repeat('a', 1000);

        $response = new ListAuthorizationModelsResponse($models, $longToken);

        expect($response->getContinuationToken())->toBe($longToken);
        expect(strlen($response->getContinuationToken()))->toBe(1000);
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        ListAuthorizationModelsResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 401 unauthorized error', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Unauthorized']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        ListAuthorizationModelsResponse::fromResponse($httpResponse, $request, $validator);
    });

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Internal server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator();

        $this->expectException(NetworkException::class);
        ListAuthorizationModelsResponse::fromResponse($httpResponse, $request, $validator);
    });
});
