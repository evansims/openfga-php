<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Language;

use OpenFGA\Language\DslTransformer;
use OpenFGA\Models\{AuthorizationModel, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schema\SchemaValidator;
use RuntimeException;

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

it('handles expressions with only whitespace correctly', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type document
          relations
            define viewer: user
        DSL;

    // This should parse fine
    $model = DslTransformer::fromDsl($dsl, $this->validator);
    expect($model)->not->toBeNull();
});

it('throws exception for unbalanced opening parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: (user or admin
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: 1 unclosed opening parenthesis');
    DslTransformer::fromDsl($dsl, $this->validator);
});

it('throws exception for unbalanced closing parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: user or admin)
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: too many closing parentheses');
    DslTransformer::fromDsl($dsl, $this->validator);
});

it('throws exception for multiple unbalanced opening parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: ((user or admin
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: 2 unclosed opening parentheses');
    DslTransformer::fromDsl($dsl, $this->validator);
});

it('handles properly balanced nested parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user
        type admin

        type document
          relations
            define owner: [user]
            define editor: [admin]
            define viewer: ((owner or editor) and (user or admin))
        DSL;

    // Should not throw any exception
    $model = DslTransformer::fromDsl($dsl, $this->validator);
    expect($model)->not->toBeNull();

    // Verify the structure was parsed correctly
    $typeDefinitions = $model->getTypeDefinitions();
    $documentType = null;
    foreach ($typeDefinitions as $td) {
        if ('document' === $td->getType()) {
            $documentType = $td;

            break;
        }
    }

    expect($documentType)->not->toBeNull();
    $relations = $documentType->getRelations();
    expect($relations->has('viewer'))->toBeTrue();
});

it('throws exception for deeply nested unbalanced parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: (user and (admin or (editor)))
        DSL;

    // Should not throw - this is balanced
    $model = DslTransformer::fromDsl($dsl, $this->validator);
    expect($model)->not->toBeNull();

    // But this should throw
    $dslUnbalanced = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: (user and (admin or (editor))
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: 1 unclosed opening parenthesis');
    DslTransformer::fromDsl($dslUnbalanced, $this->validator);
});

it('handles complex expressions with multiple operators and parentheses', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user
        type admin
        type banned

        type document
          relations
            define owner: [user]
            define editor: [admin]
            define blocked: [banned]
            define viewer: (owner or editor) but not (blocked or (banned and admin))
        DSL;

    // Should parse successfully
    $model = DslTransformer::fromDsl($dsl, $this->validator);
    expect($model)->not->toBeNull();
});

it('throws exception for mismatched parentheses in middle of expression', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: user or (admin)) and editor
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: too many closing parentheses');
    DslTransformer::fromDsl($dsl, $this->validator);
});

it('handles empty parentheses gracefully', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user

        type document
          relations
            define viewer: () or user
        DSL;

    // This should fail during parsing because () is not a valid expression
    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Input string cannot be empty');
    DslTransformer::fromDsl($dsl, $this->validator);
});

it('validates parentheses in tuple-to-userset expressions', function (): void {
    $dsl = <<<'DSL'
        model
          schema 1.1

        type user
        type group
          relations
            define member: [user]

        type document
          relations
            define parent: [group]
            define viewer: (member from parent) or [user]
        DSL;

    // Should parse successfully
    $model = DslTransformer::fromDsl($dsl, $this->validator);
    expect($model)->not->toBeNull();

    // Test unbalanced version
    $dslUnbalanced = <<<'DSL'
        model
          schema 1.1

        type user
        type group
          relations
            define member: [user]

        type document
          relations
            define parent: [group]
            define viewer: (member from parent or [user]
        DSL;

    $this->expectException(RuntimeException::class);
    $this->expectExceptionMessage('Unbalanced parentheses: 1 unclosed opening parenthesis');
    DslTransformer::fromDsl($dslUnbalanced, $this->validator);
});
