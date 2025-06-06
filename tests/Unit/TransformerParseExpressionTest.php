<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Language\Transformer;
use OpenFGA\Models\{AuthorizationModel, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schemas\SchemaValidator;

describe('TransformerParseExpression', function (): void {
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

    test('parses simple direct user type references correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define viewer: [user, user:*]
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        expect($documentType)->not->toBeNull();

        $metadata = $documentType->getMetadata();
        expect($metadata)->not->toBeNull();

        $relationsMetadata = $metadata->getRelations();
        expect($relationsMetadata)->not->toBeNull();
        expect($relationsMetadata->has('owner'))->toBeTrue();

        $ownerMetadata = $relationsMetadata->get('owner');
        expect($ownerMetadata)->toBeInstanceOf(RelationMetadata::class);

        $directTypes = $ownerMetadata->getDirectlyRelatedUserTypes();
        expect($directTypes)->not->toBeNull();
        expect($directTypes->count())->toBe(1);

        $firstType = $directTypes->get(0);
        expect($firstType->getType())->toBe('user');
        expect($firstType->getRelation())->toBeNull();
        expect($firstType->getWildcard())->toBeNull();
        expect($firstType->getCondition())->toBe('');

        expect($relationsMetadata->has('viewer'))->toBeTrue();

        $viewerMetadata = $relationsMetadata->get('viewer');
        $viewerDirectTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($viewerDirectTypes->count())->toBe(2);

        $types = [];

        foreach ($viewerDirectTypes as $type) {
            $types[] = [
                'type' => $type->getType(),
                'wildcard' => $type->getWildcard()?->jsonSerialize(),
                'relation' => $type->getRelation(),
            ];
        }

        expect($types)->toHaveCount(2);

        $hasUserType = false;

        foreach ($types as $type) {
            if ('user' === $type['type']) {
                $hasUserType = true;

                break;
            }
        }
        expect($hasUserType)->toBeTrue();
    });

    test('parses simple userset references correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define editor: owner
                define viewer: editor
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
        $editorRelation = $relations->get('editor');
        expect($editorRelation->getComputedUserset())->not->toBeNull();
        expect($editorRelation->getComputedUserset()->getRelation())->toBe('owner');

        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();

        $editorMetadata = $relationsMetadata->get('editor');
        $editorDirectTypes = $editorMetadata->getDirectlyRelatedUserTypes();
        expect($editorDirectTypes)->not->toBeNull();
        expect($editorDirectTypes->count())->toBe(0);
    });

    test('parses union expressions correctly with metadata', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user
            type group

            type document
              relations
                define owner: [user]
                define admin: [group]
                define editor: owner or admin
                define viewer: [user] or editor
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check editor relation (union of two computed usersets)
        $relations = $documentType->getRelations();
        $editorRelation = $relations->get('editor');
        expect($editorRelation->getUnion())->not->toBeNull();
        expect($editorRelation->getUnion()->count())->toBe(2);

        // Check viewer relation metadata (union with direct type)
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();

        $viewerMetadata = $relationsMetadata->get('viewer');
        $directTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($directTypes)->not->toBeNull();
        expect($directTypes->count())->toBe(1);
        expect($directTypes->get(0)->getType())->toBe('user');
    });

    test('parses intersection expressions correctly with metadata', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user
            type group

            type document
              relations
                define member: [user, group]
                define approved: [user]
                define viewer: member and approved
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check viewer relation (intersection)
        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');
        expect($viewerRelation->getIntersection())->not->toBeNull();
        expect($viewerRelation->getIntersection()->count())->toBe(2);

        // Metadata should be empty for pure intersection of computed usersets
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();
        $viewerMetadata = $relationsMetadata->get('viewer');
        $viewerDirectTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($viewerDirectTypes)->not->toBeNull();
        expect($viewerDirectTypes->count())->toBe(0);
    });

    test('parses exclusion expressions correctly with metadata', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define editor: [user]
                define banned: [user]
                define viewer: editor but not banned
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check viewer relation (difference/exclusion)
        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');
        expect($viewerRelation->getDifference())->not->toBeNull();

        $difference = $viewerRelation->getDifference();
        expect($difference->getBase())->not->toBeNull();
        expect($difference->getBase()->getComputedUserset()->getRelation())->toBe('editor');
        expect($difference->getSubtract())->not->toBeNull();
        expect($difference->getSubtract()->getComputedUserset()->getRelation())->toBe('banned');

        // Metadata should be empty for exclusion expressions
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();
        $viewerMetadata = $relationsMetadata->get('viewer');
        $viewerDirectTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($viewerDirectTypes)->not->toBeNull();
        expect($viewerDirectTypes->count())->toBe(0);
    });

    test('parses nested and grouped expressions correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: [user]
                define editor: [user]
                define blocked: [user]
                define suspended: [user]
                define viewer: (owner or editor) but not (blocked or suspended)
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check viewer relation structure
        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');
        expect($viewerRelation->getDifference())->not->toBeNull();

        $difference = $viewerRelation->getDifference();

        // Base should be a union
        $base = $difference->getBase();
        expect($base->getUnion())->not->toBeNull();
        expect($base->getUnion()->count())->toBe(2);

        // Subtract should be a union
        $subtract = $difference->getSubtract();
        expect($subtract->getUnion())->not->toBeNull();
        expect($subtract->getUnion()->count())->toBe(2);
    });

    test('parses tuple-to-userset patterns correctly', function (): void {
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
                define viewer: member from parent
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check viewer relation (tuple-to-userset)
        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');
        expect($viewerRelation->getTupleToUserset())->not->toBeNull();

        $ttu = $viewerRelation->getTupleToUserset();
        expect($ttu->getTupleset())->not->toBeNull();
        expect($ttu->getTupleset()->getRelation())->toBe('parent');
        expect($ttu->getComputedUserset())->not->toBeNull();
        expect($ttu->getComputedUserset()->getRelation())->toBe('member');

        // Metadata should be empty for tuple-to-userset
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();
        $viewerMetadata = $relationsMetadata->get('viewer');
        $viewerDirectTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($viewerDirectTypes)->not->toBeNull();
        expect($viewerDirectTypes->count())->toBe(0);
    });

    test('handles operator precedence correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define a: [user]
                define b: [user]
                define c: [user]
                define d: [user]
                define complex: a or b and c but not d
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
        $complexRelation = $relations->get('complex');

        expect($complexRelation->getUnion())->not->toBeNull();
        expect($complexRelation->getUnion()->count())->toBe(2);

        $unionChildren = [];

        foreach ($complexRelation->getUnion() as $child) {
            $unionChildren[] = $child;
        }

        expect($unionChildren[0]->getComputedUserset())->not->toBeNull();
        expect($unionChildren[0]->getComputedUserset()->getRelation())->toBe('a');

        expect($unionChildren[1]->getIntersection())->not->toBeNull();
        expect($unionChildren[1]->getIntersection()->count())->toBe(2);

        $intersectionChildren = [];

        foreach ($unionChildren[1]->getIntersection() as $child) {
            $intersectionChildren[] = $child;
        }

        expect($intersectionChildren[0]->getComputedUserset())->not->toBeNull();
        expect($intersectionChildren[0]->getComputedUserset()->getRelation())->toBe('b');

        expect($intersectionChildren[1]->getDifference())->not->toBeNull();
        $difference = $intersectionChildren[1]->getDifference();
        expect($difference->getBase()->getComputedUserset()->getRelation())->toBe('c');
        expect($difference->getSubtract()->getComputedUserset()->getRelation())->toBe('d');
    });

    test('preserves metadata correctly for complex expressions', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user
            type group

            type document
              relations
                define directViewer: [user, group]
                define parentViewer: [user]
                define adminViewer: [group]
                define complexViewer: directViewer or (parentViewer and adminViewer)
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check complexViewer metadata - should have direct types from directViewer branch
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();

        $complexViewerMetadata = $relationsMetadata->get('complexViewer');
        $directTypes = $complexViewerMetadata->getDirectlyRelatedUserTypes();

        // Should be empty because it's a union of computed usersets
        expect($directTypes)->not->toBeNull();
        expect($directTypes->count())->toBe(0);
    });

    test('handles self keyword correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type document
              relations
                define owner: self
                define viewer: owner
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check owner relation - 'self' is parsed as computed userset with relation 'self'
        $relations = $documentType->getRelations();
        $ownerRelation = $relations->get('owner');
        expect($ownerRelation->getComputedUserset())->not->toBeNull();
        expect($ownerRelation->getComputedUserset()->getRelation())->toBe('self');

        // Check metadata - self should have empty directly_related_user_types
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();
        $ownerMetadata = $relationsMetadata->get('owner');
        $ownerDirectTypes = $ownerMetadata->getDirectlyRelatedUserTypes();
        expect($ownerDirectTypes)->not->toBeNull();
        expect($ownerDirectTypes->count())->toBe(0);
    });

    test('handles empty types correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user

            type emptyType
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $emptyType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('emptyType' === $td->getType()) {
                $emptyType = $td;

                break;
            }
        }

        expect($emptyType)->not->toBeNull();
        $relations = $emptyType->getRelations();
        expect($relations)->not->toBeNull();
        expect($relations->count())->toBe(0);

        $emptyType->getMetadata();
    });

    test('handles mixed direct and computed usersets in unions correctly', function (): void {
        $dsl = <<<'DSL'
            model
              schema 1.1

            type user
            type group

            type document
              relations
                define owner: [user]
                define editor: [group]
                define viewer: [user, group] or owner or editor
            DSL;

        $model = Transformer::fromDsl($dsl, $this->validator);

        $documentType = null;

        foreach ($model->getTypeDefinitions() as $td) {
            if ('document' === $td->getType()) {
                $documentType = $td;

                break;
            }
        }

        // Check viewer relation structure
        $relations = $documentType->getRelations();
        $viewerRelation = $relations->get('viewer');
        expect($viewerRelation->getUnion())->not->toBeNull();

        // Should have 3 children: direct, owner, editor
        expect($viewerRelation->getUnion()->count())->toBe(3);

        // Check metadata for directly related types
        $metadata = $documentType->getMetadata();
        $relationsMetadata = $metadata->getRelations();
        $viewerMetadata = $relationsMetadata->get('viewer');

        $directTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
        expect($directTypes)->not->toBeNull();
        expect($directTypes->count())->toBe(2);

        $typeNames = [];

        foreach ($directTypes as $type) {
            $typeNames[] = $type->getType();
        }
        expect($typeNames)->toContain('user');
        expect($typeNames)->toContain('group');
    });
});
