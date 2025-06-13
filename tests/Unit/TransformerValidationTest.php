<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Exceptions\SerializationException;
use OpenFGA\{Messages, Transformer};
use OpenFGA\Models\{AuthorizationModel, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schemas\SchemaValidator;

describe('TransformerValidation', function (): void {
    beforeEach(function (): void {
        $this->validator = new SchemaValidator;
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

    test('handles expressions with only whitespace correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type document
              relations
                define viewer: user
            DSL;

        // This should parse fine
        $model = Transformer::fromDsl($dsl, $this->validator);
        expect($model)->not->toBeNull();
    });

    test('throws exception for unbalanced opening parentheses', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: (user or admin
            DSL;

        Transformer::fromDsl($dsl, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: 1 unclosed opening parenthesis');

    test('throws exception for unbalanced closing parentheses', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: user or admin)
            DSL;

        Transformer::fromDsl($dsl, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: too many closing parentheses');

    test('throws exception for multiple unbalanced opening parentheses', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: ((user or admin
            DSL;

        Transformer::fromDsl($dsl, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: 2 unclosed opening parentheses');

    test('handles properly balanced nested parentheses', function (): void {
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
        $model = Transformer::fromDsl($dsl, $this->validator);
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

    test('throws exception for deeply nested unbalanced parentheses - valid case', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: (user and (admin or (editor)))
            DSL;

        // Should not throw - this is balanced
        $model = Transformer::fromDsl($dsl, $this->validator);
        expect($model)->not->toBeNull();
    });

    test('throws exception for deeply nested unbalanced parentheses - invalid case', function (): void {
        $dslUnbalanced = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: (user and (admin or (editor))
            DSL;

        Transformer::fromDsl($dslUnbalanced, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: 1 unclosed opening parenthesis');

    test('handles complex expressions with multiple operators and parentheses', function (): void {
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
        $model = Transformer::fromDsl($dsl, $this->validator);
        expect($model)->not->toBeNull();
    });

    test('throws exception for mismatched parentheses in middle of expression', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: user or (admin)) and editor
            DSL;

        Transformer::fromDsl($dsl, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: too many closing parentheses');

    test('handles empty parentheses gracefully', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define viewer: () or user
            DSL;

        // This should fail during parsing because () is not a valid expression
        Transformer::fromDsl($dsl, $this->validator);
    })->throws(SerializationException::class, trans(Messages::DSL_INPUT_EMPTY));

    test('validates parentheses in tuple-to-userset expressions - valid case', function (): void {
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
        $model = Transformer::fromDsl($dsl, $this->validator);
        expect($model)->not->toBeNull();
    });

    test('validates parentheses in tuple-to-userset expressions - unbalanced case', function (): void {
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

        Transformer::fromDsl($dslUnbalanced, $this->validator);
    })->throws(SerializationException::class, 'Unbalanced parentheses: 1 unclosed opening parenthesis');
});
