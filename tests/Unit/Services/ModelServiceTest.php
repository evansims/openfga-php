<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use Exception;
use OpenFGA\Language;
use OpenFGA\Models\AuthorizationModelInterface;
use OpenFGA\Models\Collections\{AuthorizationModelsInterface, ConditionsInterface, TypeDefinitions, TypeDefinitionsInterface};
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Models\{Store, TypeDefinition};
use OpenFGA\Repositories\ModelRepositoryInterface;
use OpenFGA\Results\{Failure, Success};
use OpenFGA\Services\{ModelService, ModelServiceInterface};

beforeEach(function (): void {
    $this->mockModelRepository = test()->createMock(ModelRepositoryInterface::class);

    $this->service = new ModelService(
        $this->mockModelRepository,
        Language::English,
    );

    $this->store = new Store(
        'store-123',
        'Test Store',
        new DateTimeImmutable,
        new DateTimeImmutable,
    );
    $this->typeDefinitions = new TypeDefinitions([
        new TypeDefinition('document'),
    ]);
});

describe('ModelService', function (): void {
    it('implements ModelServiceInterface', function (): void {
        expect($this->service)->toBeInstanceOf(ModelServiceInterface::class);
    });

    describe('validateModel', function (): void {
        it('validates successful with valid type definitions', function (): void {
            $result = $this->service->validateModel($this->typeDefinitions);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBeTrue();
        });

        it('fails validation for empty type definitions', function (): void {
            $emptyTypeDefinitions = new TypeDefinitions([]);

            $result = $this->service->validateModel($emptyTypeDefinitions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('detects duplicate type names', function (): void {
            $duplicateTypeDefinitions = new TypeDefinitions([
                new TypeDefinition('document'),
                new TypeDefinition('document'),
            ]);

            $result = $this->service->validateModel($duplicateTypeDefinitions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('supports different schema versions', function (): void {
            $result = $this->service->validateModel(
                $this->typeDefinitions,
                SchemaVersion::V1_0,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });
    });

    describe('createModel', function (): void {
        it('validates type definitions before creating model', function (): void {
            $emptyTypeDefinitions = new TypeDefinitions([]);

            $result = $this->service->createModel(
                $emptyTypeDefinitions,
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('detects duplicate type definitions', function (): void {
            $duplicateTypeDefinitions = new TypeDefinitions([
                new TypeDefinition('document'),
                new TypeDefinition('document'), // Duplicate
            ]);

            $result = $this->service->createModel(
                $duplicateTypeDefinitions,
            );

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('listAllModels', function (): void {
        it('forwards pagination parameters to the repository', function (): void {
            $continuationToken = 'token-abc';
            $pageSize = 10;

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->with($pageSize, $continuationToken)
                ->willReturn(new Success(null));

            $result = $this->service->listAllModels(
                continuationToken: $continuationToken,
                pageSize: $pageSize,
            );

            expect($result)->toBeInstanceOf(Success::class);
        });

        it('forwards failure from repository', function (): void {
            $failure = new Failure(new Exception('Repository error'));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->willReturn($failure);

            $result = $this->service->listAllModels();

            expect($result)->toBe($failure);
        });
    });

    describe('findModel', function (): void {
        it('delegates to repository for model retrieval', function (): void {
            $modelId = 'model-123';
            $success = new Success(null);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('get')
                ->with($modelId)
                ->willReturn($success);

            $result = $this->service->findModel($modelId);

            expect($result)->toBe($success);
        });

        it('forwards failure from repository', function (): void {
            $modelId = 'model-123';
            $failure = new Failure(new Exception('Model not found'));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('get')
                ->with($modelId)
                ->willReturn($failure);

            $result = $this->service->findModel($modelId);

            expect($result)->toBe($failure);
        });
    });

    describe('cloneModel', function (): void {
        it('clones model successfully', function (): void {
            $sourceModelId = 'model-123';
            $mockModel = test()->createMock(AuthorizationModelInterface::class);
            $mockTypeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
            $mockConditions = test()->createMock(ConditionsInterface::class);

            $mockModel->method('getTypeDefinitions')->willReturn($mockTypeDefinitions);
            $mockModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);
            $mockModel->method('getConditions')->willReturn($mockConditions);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('get')
                ->with($sourceModelId)
                ->willReturn(new Success($mockModel));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('create')
                ->with($mockTypeDefinitions, SchemaVersion::V1_1, $mockConditions)
                ->willReturn(new Success(null));

            $result = $this->service->cloneModel($sourceModelId);

            expect($result)->toBeInstanceOf(Success::class);
        });

        it('returns failure when source model not found', function (): void {
            $sourceModelId = 'model-123';
            $failure = new Failure(new Exception('Model not found'));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('get')
                ->with($sourceModelId)
                ->willReturn($failure);

            $this->mockModelRepository
                ->expects(test()->never())
                ->method('create');

            $result = $this->service->cloneModel($sourceModelId);

            expect($result)->toBe($failure);
        });

        it('returns failure when model creation fails', function (): void {
            $sourceModelId = 'model-123';
            $mockModel = test()->createMock(AuthorizationModelInterface::class);
            $mockTypeDefinitions = test()->createMock(TypeDefinitionsInterface::class);
            $mockConditions = test()->createMock(ConditionsInterface::class);
            $createFailure = new Failure(new Exception('Creation failed'));

            $mockModel->method('getTypeDefinitions')->willReturn($mockTypeDefinitions);
            $mockModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);
            $mockModel->method('getConditions')->willReturn($mockConditions);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('get')
                ->with($sourceModelId)
                ->willReturn(new Success($mockModel));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('create')
                ->willReturn($createFailure);

            $result = $this->service->cloneModel($sourceModelId);

            expect($result)->toBe($createFailure);
        });
    });

    describe('getLatestModel', function (): void {
        it('returns latest model when models exist', function (): void {
            $mockModels = test()->createMock(AuthorizationModelsInterface::class);
            $mockModel = test()->createMock(AuthorizationModelInterface::class);

            $mockModels->method('count')->willReturn(1);
            $mockModels->method('first')->willReturn($mockModel);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->with(1)
                ->willReturn(new Success($mockModels));

            $result = $this->service->getLatestModel($this->store);

            expect($result)->toBeInstanceOf(Success::class);
            expect($result->unwrap())->toBe($mockModel);
        });

        it('returns failure when no models exist', function (): void {
            $mockModels = test()->createMock(AuthorizationModelsInterface::class);
            $mockModels->method('count')->willReturn(0);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->with(1)
                ->willReturn(new Success($mockModels));

            $result = $this->service->getLatestModel($this->store);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('forwards failure from repository', function (): void {
            $failure = new Failure(new Exception('List failed'));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->with(1)
                ->willReturn($failure);

            $result = $this->service->getLatestModel($this->store);

            expect($result)->toBe($failure);
        });

        it('handles string store ID', function (): void {
            $storeId = 'store-456';
            $mockModels = test()->createMock(AuthorizationModelsInterface::class);
            $mockModels->method('count')->willReturn(0);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('list')
                ->with(1)
                ->willReturn(new Success($mockModels));

            $result = $this->service->getLatestModel($storeId);

            expect($result)->toBeInstanceOf(Failure::class);
        });
    });

    describe('createModel', function (): void {
        it('creates model successfully when validation passes', function (): void {
            $success = new Success(null);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('create')
                ->with($this->typeDefinitions, SchemaVersion::V1_1, null)
                ->willReturn($success);

            $result = $this->service->createModel($this->typeDefinitions);

            expect($result)->toBe($success);
        });

        it('returns validation failure before attempting creation', function (): void {
            $emptyTypeDefinitions = new TypeDefinitions([]);

            $this->mockModelRepository
                ->expects(test()->never())
                ->method('create');

            $result = $this->service->createModel($emptyTypeDefinitions);

            expect($result)->toBeInstanceOf(Failure::class);
        });

        it('forwards repository creation failure', function (): void {
            $failure = new Failure(new Exception('Creation failed'));

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('create')
                ->willReturn($failure);

            $result = $this->service->createModel($this->typeDefinitions);

            expect($result)->toBe($failure);
        });

        it('supports custom schema version and conditions', function (): void {
            $mockConditions = test()->createMock(ConditionsInterface::class);
            $success = new Success(null);

            $this->mockModelRepository
                ->expects(test()->once())
                ->method('create')
                ->with($this->typeDefinitions, SchemaVersion::V1_0, $mockConditions)
                ->willReturn($success);

            $result = $this->service->createModel(
                $this->typeDefinitions,
                $mockConditions,
                SchemaVersion::V1_0,
            );

            expect($result)->toBe($success);
        });
    });
});
