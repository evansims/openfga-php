<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Language\Transformer;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, ObjectRelationInterface, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, TypeDefinitionInterface, UserTypeFilter, Userset, UsersetInterface};
use OpenFGA\Exceptions\SerializationError;
use OpenFGA\Messages;
// AuthorizationModelInterface is imported in the group above
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
// ObjectRelationInterface is imported in the group above
// TypeDefinitionInterface is imported in the group above
// UsersetInterface is imported in the group above
use OpenFGA\Models\Enums\SchemaVersion;
use OpenFGA\Schemas\SchemaValidator;
// The large group 'use OpenFGA\Models\{...}' was duplicated and is removed here. The first one (line 4 in original) is kept and extended.
use OpenFGA\Translation\Translator;
use PHPUnit\Framework\TestCase;

describe('Transformer', function (): void {
    // Add a beforeEach to handle common schema registrations if needed, or ensure each test does it.
    // For now, individual tests will handle their schema validator setup.

    test('transforms DSL to model and back', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: self
                define writer: user
            DSL;

        $validator = new SchemaValidator;

        // Register schemas used by AuthorizationModel
        $validator
            ->registerSchema(AuthorizationModel::schema())
            ->registerSchema(TypeDefinitions::schema())
            ->registerSchema(TypeDefinition::schema())
            ->registerSchema(TypeDefinitionRelations::schema())
            ->registerSchema(Userset::schema())
            ->registerSchema(Usersets::schema())
            ->registerSchema(ObjectRelation::schema())
            ->registerSchema(Conditions::schema())
            ->registerSchema(Condition::schema())
            ->registerSchema(Metadata::schema())
            ->registerSchema(RelationMetadataCollection::schema())
            ->registerSchema(RelationMetadata::schema())
            ->registerSchema(RelationReferences::schema())
            ->registerSchema(RelationReference::schema())
            ->registerSchema(SourceInfo::schema())
            ->registerSchema(ConditionParameters::schema())
            ->registerSchema(ConditionParameter::schema())
            ->registerSchema(ConditionMetadata::schema())
            ->registerSchema(TupleToUsersetV1::schema())
            ->registerSchema(DifferenceV1::schema())
            ->registerSchema(UserTypeFilter::schema())
            ->registerSchema(UserTypeFilters::schema());

        $model = Transformer::fromDsl($dsl, $validator);

        expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
        expect($model->getSchemaVersion()->value)->toBe('1.1');

        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toBe(trim($dsl));
    });

    test('handles composite exclusion expressions with but not', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define banned: user
                define muted: user
                define viewer: user
                define reader: viewer but not (banned or muted)
            DSL;

        $validator = new SchemaValidator;

        // Register schemas used by AuthorizationModel
        $validator
            ->registerSchema(AuthorizationModel::schema())
            ->registerSchema(TypeDefinitions::schema())
            ->registerSchema(TypeDefinition::schema())
            ->registerSchema(TypeDefinitionRelations::schema())
            ->registerSchema(Userset::schema())
            ->registerSchema(Usersets::schema())
            ->registerSchema(ObjectRelation::schema())
            ->registerSchema(Conditions::schema())
            ->registerSchema(Condition::schema())
            ->registerSchema(Metadata::schema())
            ->registerSchema(RelationMetadataCollection::schema())
            ->registerSchema(RelationMetadata::schema())
            ->registerSchema(RelationReferences::schema())
            ->registerSchema(RelationReference::schema())
            ->registerSchema(SourceInfo::schema())
            ->registerSchema(ConditionParameters::schema())
            ->registerSchema(ConditionParameter::schema())
            ->registerSchema(ConditionMetadata::schema())
            ->registerSchema(TupleToUsersetV1::schema())
            ->registerSchema(DifferenceV1::schema())
            ->registerSchema(UserTypeFilter::schema())
            ->registerSchema(UserTypeFilters::schema());

        $model = Transformer::fromDsl($dsl, $validator);

        expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);

        // Verify the reader relation has the correct structure
        $typeDefinitions = $model->getTypeDefinitions();
        expect($typeDefinitions->count())->toBe(2);

        $documentType = null;

        foreach ($typeDefinitions as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        expect($documentType)->not->toBeNull();
        $relations = $documentType->getRelations();
        expect($relations)->not->toBeNull();
        expect($relations->has('reader'))->toBeTrue();

        $readerRelation = $relations->get('reader');
        expect($readerRelation->getDifference())->not->toBeNull();

        $difference = $readerRelation->getDifference();
        expect($difference->getBase()->getComputedUserset())->not->toBeNull();
        expect($difference->getBase()->getComputedUserset()->getRelation())->toBe('viewer');

        $subtract = $difference->getSubtract();
        expect($subtract->getUnion())->not->toBeNull();

        $unionChildren = $subtract->getUnion();
        expect($unionChildren->count())->toBe(2);

        // Verify the DSL round-trip
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toBe(trim($dsl));
    });

    test('handles complex DSL parsing edge cases', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            # This is a comment

            type user

            # Another comment

            type document
              relations
                define owner: [user]
                define viewer: owner
                define reader: (viewer)
            DSL;

        $validator = new SchemaValidator;
        $validator
            ->registerSchema(AuthorizationModel::schema())
            ->registerSchema(TypeDefinitions::schema())
            ->registerSchema(TypeDefinition::schema())
            ->registerSchema(TypeDefinitionRelations::schema())
            ->registerSchema(Userset::schema())
            ->registerSchema(Usersets::schema())
            ->registerSchema(ObjectRelation::schema())
            ->registerSchema(Metadata::schema())
            ->registerSchema(RelationMetadataCollection::schema())
            ->registerSchema(RelationMetadata::schema())
            ->registerSchema(RelationReferences::schema())
            ->registerSchema(RelationReference::schema())
            ->registerSchema(SourceInfo::schema())
            ->registerSchema(ConditionParameters::schema())
            ->registerSchema(ConditionParameter::schema())
            ->registerSchema(ConditionMetadata::schema())
            ->registerSchema(TupleToUsersetV1::schema())
            ->registerSchema(DifferenceV1::schema())
            ->registerSchema(UserTypeFilter::schema())
            ->registerSchema(UserTypeFilters::schema())
            ->registerSchema(Conditions::schema())
            ->registerSchema(Condition::schema());

        $model = Transformer::fromDsl($dsl, $validator);

        expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
        expect($model->getTypeDefinitions()->count())->toBe(2);

        // Test round-trip to ensure parsing is correct
        $resultDsl = Transformer::toDsl($model);
        expect($resultDsl)->toContain('type user');
        expect($resultDsl)->toContain('type document');
    });

    test('handles variable whitespace around or operator', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define banned: user
                define muted: user
                define suspended: user
                define viewer: user
                define reader: viewer but not (banned    or     muted  or    suspended)
            DSL;

        $validator = new SchemaValidator;

        // Register schemas used by AuthorizationModel
        $validator
            ->registerSchema(AuthorizationModel::schema())
            ->registerSchema(TypeDefinitions::schema())
            ->registerSchema(TypeDefinition::schema())
            ->registerSchema(TypeDefinitionRelations::schema())
            ->registerSchema(Userset::schema())
            ->registerSchema(Usersets::schema())
            ->registerSchema(ObjectRelation::schema())
            ->registerSchema(Conditions::schema())
            ->registerSchema(Condition::schema())
            ->registerSchema(Metadata::schema())
            ->registerSchema(RelationMetadataCollection::schema())
            ->registerSchema(RelationMetadata::schema())
            ->registerSchema(RelationReferences::schema())
            ->registerSchema(RelationReference::schema())
            ->registerSchema(SourceInfo::schema())
            ->registerSchema(ConditionParameters::schema())
            ->registerSchema(ConditionParameter::schema())
            ->registerSchema(ConditionMetadata::schema())
            ->registerSchema(TupleToUsersetV1::schema())
            ->registerSchema(DifferenceV1::schema())
            ->registerSchema(UserTypeFilter::schema())
            ->registerSchema(UserTypeFilters::schema());

        $model = Transformer::fromDsl($dsl, $validator);

        expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);

        // Verify the reader relation has the correct structure with three union children
        $typeDefinitions = $model->getTypeDefinitions();
        expect($typeDefinitions->count())->toBe(2);

        $documentType = null;

        foreach ($typeDefinitions as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        expect($documentType)->not->toBeNull();
        $relations = $documentType->getRelations();
        expect($relations)->not->toBeNull();
        expect($relations->has('reader'))->toBeTrue();

        $readerRelation = $relations->get('reader');
        expect($readerRelation->getDifference())->not->toBeNull();

        $difference = $readerRelation->getDifference();
        expect($difference->getBase()->getComputedUserset())->not->toBeNull();
        expect($difference->getBase()->getComputedUserset()->getRelation())->toBe('viewer');

        $subtract = $difference->getSubtract();
        expect($subtract->getUnion())->not->toBeNull();

        $unionChildren = $subtract->getUnion();
        expect($unionChildren->count())->toBe(3); // Should parse banned, muted, and suspended

        // Verify that all three relations are present in the union
        $relationNames = [];

        foreach ($unionChildren as $child) {
            if ($child->getComputedUserset()) {
                $relationNames[] = $child->getComputedUserset()->getRelation();
            }
        }

        expect($relationNames)->toContain('banned');
        expect($relationNames)->toContain('muted');
        expect($relationNames)->toContain('suspended');
    });

    // New tests for computed userset relation validation
    test('toDsl throws error for computed userset with null relation', function (): void {
        /** @var TestCase $this */
        $this->expectException(\OpenFGA\Exceptions\SerializationException::class);
        $this->expectExceptionMessage(Translator::trans(Messages::DSL_INVALID_COMPUTED_USERSET_RELATION));

        $mockModel = $this->createMock(AuthorizationModelInterface::class);
        $mockObjectRelation = $this->createMock(ObjectRelationInterface::class);

        $mockObjectRelation->method('getRelation')->willReturn(null);
        $mockObjectRelation->method('getObject')->willReturn('document');

        // Create a real Userset object configured to return the mockObjectRelation
        $realUserset = new Userset(
            direct: null,
            computedUserset: $mockObjectRelation,
            tupleToUserset: null,
            union: null,
            intersection: null,
            difference: null
        );

        $relationsArray = ['somerel' => $realUserset];
        $realRelationsCollection = new TypeDefinitionRelations($relationsArray);

        $realTypeDef = new TypeDefinition(
            type: 'document',
            relations: $realRelationsCollection,
            metadata: null
        );

        $typeDefsArray = [$realTypeDef];
        // Ensure TypeDefinitions can be instantiated with an array of TypeDefinitionInterface
        $typeDefinitionsCollection = new TypeDefinitions($typeDefsArray);

        $mockModel->method('getTypeDefinitions')->willReturn($typeDefinitionsCollection);
        $mockModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);
        $mockModel->method('getConditions')->willReturn(null);

        Transformer::toDsl($mockModel);
    })->uses(TestCase::class); // Indicates that Pest should use PHPUnit's TestCase context for this test

    test('toDsl throws error for computed userset with empty relation', function (): void {
        /** @var TestCase $this */
        $this->expectException(\OpenFGA\Exceptions\SerializationException::class);
        $this->expectExceptionMessage(Translator::trans(Messages::DSL_INVALID_COMPUTED_USERSET_RELATION));

        $mockModel = $this->createMock(AuthorizationModelInterface::class);
        $mockObjectRelation = $this->createMock(ObjectRelationInterface::class);

        $mockObjectRelation->method('getRelation')->willReturn(''); // Empty string
        $mockObjectRelation->method('getObject')->willReturn('document');

        // Create a real Userset object configured to return the mockObjectRelation
        $realUserset = new Userset(
            direct: null,
            computedUserset: $mockObjectRelation,
            tupleToUserset: null,
            union: null,
            intersection: null,
            difference: null
        );

        $relationsArray = ['somerel' => $realUserset];
        $realRelationsCollection = new TypeDefinitionRelations($relationsArray);

        $realTypeDef = new TypeDefinition(
            type: 'document',
            relations: $realRelationsCollection,
            metadata: null
        );

        $typeDefsArray = [$realTypeDef];
        $typeDefinitionsCollection = new TypeDefinitions($typeDefsArray);

        $mockModel->method('getTypeDefinitions')->willReturn($typeDefinitionsCollection);
        $mockModel->method('getSchemaVersion')->willReturn(SchemaVersion::V1_1);
        $mockModel->method('getConditions')->willReturn(null);

        Transformer::toDsl($mockModel);
    })->uses(TestCase::class); // Indicates that Pest should use PHPUnit's TestCase context for this test
});
