<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Language;

use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\{AuthorizationModel, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schema\SchemaValidator;

beforeEach(function (): void {
    $this->validator = new SchemaValidator();
    $this->validator
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
});

it('verifies "or" operator already uses regex for flexible matching', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define owner: [user]
            define editor: [user]
            define viewer: owner   OR   editor
        DSL;

    $model = DslTransformer::fromDsl($dsl, $this->validator);

    $documentType = null;
    foreach ($model->getTypeDefinitions() as $td) {
        if ('document' === $td->getType()) {
            $documentType = $td;

            break;
        }
    }

    $relations = $documentType->getRelations();
    $viewerRelation = $relations->get('viewer');

    expect($viewerRelation->getUnion())->not->toBeNull();
    expect($viewerRelation->getUnion()->count())->toBe(2);
});

it('handles mixed case OR correctly', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define owner: [user]
            define editor: [user]
            define viewer: owner Or editor
        DSL;

    $model = DslTransformer::fromDsl($dsl, $this->validator);

    $documentType = null;
    foreach ($model->getTypeDefinitions() as $td) {
        if ('document' === $td->getType()) {
            $documentType = $td;

            break;
        }
    }

    $relations = $documentType->getRelations();
    $viewerRelation = $relations->get('viewer');

    expect($viewerRelation->getUnion())->not->toBeNull();
    expect($viewerRelation->getUnion()->count())->toBe(2);
});
