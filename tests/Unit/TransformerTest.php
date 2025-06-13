<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, ObjectRelationInterface, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, TypeDefinitionInterface, UserTypeFilter, Userset, UsersetInterface};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
// AuthorizationModelInterface is imported in the group above
use OpenFGA\Schemas\SchemaValidator;
// ObjectRelationInterface is imported in the group above
// TypeDefinitionInterface is imported in the group above
// UsersetInterface is imported in the group above
use OpenFGA\Transformer;
use ReflectionMethod;

// The large group 'use OpenFGA\Models\{...}' was duplicated and is removed here. The first one (line 4 in original) is kept and extended.

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

    test('toDsl correctly renders computed userset with valid relation', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: user
                define viewer: editor
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toBe(trim($dsl));
    });

    test('testSplitRespectingParenthesesWithRegexHandlesZeroLengthMatch', function (): void {
        $transformer = new Transformer;
        $method = new ReflectionMethod(Transformer::class, 'splitRespectingParenthesesWithRegex');
        $method->setAccessible(true);

        $input = 'a or (b or c)';
        $pattern = '/\s*or\s*/';
        $expectedOutput = ['a', '(b or c)'];

        $result = $method->invoke($transformer, $input, $pattern);
        expect($result)->toBe($expectedOutput);
    });

    test('testRendersAndOrPrecedenceCorrectly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type A_type
            type B_type
            type C_type
            type document
              relations
                define a_rel: [A_type]
                define b_rel: [B_type]
                define c_rel: [C_type]
                define test_relation: (a_rel or b_rel) and c_rel
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toContain('define test_relation: (a_rel or b_rel) and c_rel');
    });

    test('testRendersAndOrPrecedenceCorrectlyWithParentheses', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type A_type
            type B_type
            type C_type
            type document
              relations
                define a_rel: [A_type]
                define b_rel: [B_type]
                define c_rel: [C_type]
                define test_relation: a_rel and (b_rel or c_rel)
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toContain('define test_relation: a_rel and (b_rel or c_rel)');
    });

    test('testRendersOrAndPrecedenceCorrectly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type A_type
            type B_type
            type C_type
            type D_type
            type document
              relations
                define a_rel: [A_type]
                define b_rel: [B_type]
                define c_rel: [C_type]
                define d_rel: [D_type]
                define test_relation: (a_rel and b_rel) or (c_rel and d_rel)
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toContain('define test_relation: (a_rel and b_rel) or (c_rel and d_rel)');
    });

    test('testRendersOrButNotPrecedenceCorrectly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type A_type
            type B_type
            type C_type
            type document
              relations
                define a_rel: [A_type]
                define b_rel: [B_type]
                define c_rel: [C_type]
                define test_relation: (a_rel or b_rel) but not c_rel
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toContain('define test_relation: (a_rel or b_rel) but not c_rel');
    });

    test('testRendersAndButNotPrecedenceCorrectly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1
            type user
            type A_type
            type B_type
            type C_type
            type document
              relations
                define a_rel: [A_type]
                define b_rel: [B_type]
                define c_rel: [C_type]
                define test_relation: (a_rel and b_rel) but not c_rel
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
        $resultDsl = Transformer::toDsl($model);
        expect(trim($resultDsl))->toContain('define test_relation: (a_rel and b_rel) but not c_rel');
    });
});
