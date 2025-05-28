<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Language;

use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schema\SchemaValidator;

it('transforms DSL to model and back', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: self
            define writer: user
        DSL;

    $validator = new SchemaValidator();

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

    $model = DslTransformer::fromDsl($dsl, $validator);

    expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
    expect($model->getSchemaVersion()->value)->toBe('1.1');

    $resultDsl = DslTransformer::toDsl($model);
    expect(trim($resultDsl))->toBe(trim($dsl));
});

it('handles composite exclusion expressions with but not', function (): void {
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

    $validator = new SchemaValidator();

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

    $model = DslTransformer::fromDsl($dsl, $validator);

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
    $resultDsl = DslTransformer::toDsl($model);
    expect(trim($resultDsl))->toBe(trim($dsl));
});
