<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Services;

use DateTimeImmutable;
use OpenFGA\Language;
use OpenFGA\Models\Collections\TypeDefinitions;
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
    });
});
