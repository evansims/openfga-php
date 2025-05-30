<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Models\{AuthorizationModel, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schema\SchemaValidator;
use OpenFGA\Transformer;

describe('TransformerButNot', function (): void {
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

    test('handles standard "but not" spacing correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor but not blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();
        expect($viewerRelation->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($viewerRelation->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });

    test('handles extra spacing in "but not" correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor   but     not   blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();
        expect($viewerRelation->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($viewerRelation->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });

    test('handles uppercase "BUT NOT" correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor BUT NOT blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();
        expect($viewerRelation->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($viewerRelation->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });

    test('handles mixed case "But Not" correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor But Not blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();
        expect($viewerRelation->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($viewerRelation->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });

    test('handles tabs around "but not" correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor	but	not	blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();
        expect($viewerRelation->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($viewerRelation->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });

    test('handles multiple "but not" operations correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define suspended: [user]
                define blocked: [user]
                define viewer: owner but not suspended but not blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        // Should create nested difference operations: (owner but not suspended) but not blocked
        expect($viewerRelation->getDifference())->not->toBeNull();

        $outerDifference = $viewerRelation->getDifference();
        expect($outerDifference->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');

        // The base should be another difference
        expect($outerDifference->getBase()->getDifference())->not->toBeNull();
        $innerDifference = $outerDifference->getBase()->getDifference();
        expect($innerDifference->getBase()->getComputedUserset()->getRelation())->toBe('owner');
        expect($innerDifference->getSubtract()->getComputedUserset()->getRelation())->toBe('suspended');
    });

    test('handles complex expressions with parentheses and "but not"', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define editor: [user]
                define suspended: [user]
                define blocked: [user]
                define viewer: (owner or editor) BUT NOT (suspended or blocked)
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();

        // Base should be a union
        $base = $viewerRelation->getDifference()->getBase();
        expect($base->getUnion())->not->toBeNull();
        expect($base->getUnion()->count())->toBe(2);

        // Subtract should be a union
        $subtract = $viewerRelation->getDifference()->getSubtract();
        expect($subtract->getUnion())->not->toBeNull();
        expect($subtract->getUnion()->count())->toBe(2);
    });

    test('handles "but not" with mixed operators and various spacing', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user
            type admin

            type document
              relations
                define owner: [user]
                define admin: [admin]
                define banned: [user]
                define viewer: (owner   OR   admin)   BuT   nOt   banned
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        expect($viewerRelation->getDifference())->not->toBeNull();

        // Base should be a union (owner OR admin)
        $base = $viewerRelation->getDifference()->getBase();
        expect($base->getUnion())->not->toBeNull();
        expect($base->getUnion()->count())->toBe(2);

        // Subtract should be banned
        $subtract = $viewerRelation->getDifference()->getSubtract();
        expect($subtract->getComputedUserset())->not->toBeNull();
        expect($subtract->getComputedUserset()->getRelation())->toBe('banned');
    });

    test('correctly generates DSL output with proper "but not" formatting', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define blocked: [user]
                define viewer: editor   BUT   NOT   blocked
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);
        $outputDsl = Transformer::toDsl($model);

        // The output should normalize to standard spacing
        expect($outputDsl)->toContain('define viewer: editor but not blocked');
    });

    test('handles "but not" at the beginning of parentheses', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define editor: [user]
                define blocked: [user]
                define viewer: owner or (editor but not blocked)
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;
        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');

        // Should be a union with owner and (editor but not blocked)
        expect($viewerRelation->getUnion())->not->toBeNull();
        expect($viewerRelation->getUnion()->count())->toBe(2);

        // Second child should be a difference
        $unionChildren = [];
        foreach ($viewerRelation->getUnion() as $child) {
            $unionChildren[] = $child;
        }

        expect($unionChildren[1]->getDifference())->not->toBeNull();
        expect($unionChildren[1]->getDifference()->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($unionChildren[1]->getDifference()->getSubtract()->getComputedUserset()->getRelation())->toBe('blocked');
    });
});
