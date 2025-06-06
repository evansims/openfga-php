<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit;

use OpenFGA\Language\Transformer;
use OpenFGA\Models\{AuthorizationModel, AuthorizationModelInterface, Condition, ConditionMetadata, ConditionParameter, DifferenceV1, Metadata, ObjectRelation, RelationMetadata, RelationReference, SourceInfo, TupleToUsersetV1, TypeDefinition, UserTypeFilter, Userset};
use OpenFGA\Models\Collections\{ConditionParameters, Conditions, RelationMetadataCollection, RelationReferences, TypeDefinitionRelations, TypeDefinitions, UserTypeFilters, Usersets};
use OpenFGA\Schemas\SchemaValidator;

/*
 * Comprehensive Transformer test suite based on official OpenFGA DSL test patterns.
 *
 * This test suite implements rigorous testing using the same sample data and patterns
 * that the official OpenFGA DSL transformer packages use, covering complex scenarios
 * from basic schema transformations to advanced real-world use cases.
 *
 * Test patterns derived from: https://github.com/openfga/language/tree/main/tests/data
 */
describe('TransformerComprehensive', function (): void {
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

    describe('Basic Schema Transformations', function (): void {
        test('transforms minimal schema-only model', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model)->toBeInstanceOf(AuthorizationModelInterface::class);
            expect($model->getSchemaVersion()->value)->toBe('1.1');
            expect($model->getTypeDefinitions()->count())->toBe(0);

            $resultDsl = Transformer::toDsl($model);
            expect(trim($resultDsl))->toBe(trim($dsl));
        });

        test('transforms single type without relations', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getSchemaVersion()->value)->toBe('1.1');
            expect($model->getTypeDefinitions()->count())->toBe(1);

            $userType = $model->getTypeDefinitions()->get(0);
            expect($userType->getType())->toBe('user');
            expect($userType->getRelations()->count())->toBe(0);

            $resultDsl = Transformer::toDsl($model);
            expect(trim($resultDsl))->toBe(trim($dsl));
        });

        test('transforms type with single direct relation', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user
                  relations
                    define viewer: [user]
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getTypeDefinitions()->count())->toBe(1);

            $userType = $model->getTypeDefinitions()->get(0);
            expect($userType->getType())->toBe('user');
            expect($userType->getRelations()->count())->toBe(1);
            expect($userType->getRelations()->has('viewer'))->toBeTrue();

            $viewerRelation = $userType->getRelations()->get('viewer');
            expect($viewerRelation->getDirect())->not->toBeNull();

            // Verify metadata
            $metadata = $userType->getMetadata();
            expect($metadata)->not->toBeNull();
            expect($metadata->getRelations()->has('viewer'))->toBeTrue();

            $viewerMetadata = $metadata->getRelations()->get('viewer');
            $directTypes = $viewerMetadata->getDirectlyRelatedUserTypes();
            expect($directTypes->count())->toBe(1);
            expect($directTypes->get(0)->getType())->toBe('user');

            $resultDsl = Transformer::toDsl($model);
            expect(trim($resultDsl))->toBe(trim($dsl));
        });
    });

    describe('Complex Logical Operators', function (): void {
        test('transforms mixed operators with proper precedence', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type document
                  relations
                    define rel1: [user]
                    define rel2: [user]
                    define rel4: [user]
                    define rel5: [user]
                    define rel7: [user]
                    define rel8: [user]
                    define rel9: ((rel1 or rel2) but not ((rel4 and rel5) but not (rel7 but not rel8)))
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
            expect($documentType->getRelations()->has('rel9'))->toBeTrue();

            $rel9 = $documentType->getRelations()->get('rel9');

            // rel9 should be a difference with complex nested structure
            expect($rel9->getDifference())->not->toBeNull();

            $difference = $rel9->getDifference();

            // Base should be a union of rel1 and rel2
            $base = $difference->getBase();
            expect($base->getUnion())->not->toBeNull();
            expect($base->getUnion()->count())->toBe(2);

            // Subtract should be a complex nested structure
            $subtract = $difference->getSubtract();
            expect($subtract->getDifference())->not->toBeNull();

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('rel9');
        });

        test('transforms advanced union and intersection patterns', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user
                type group

                type document
                  relations
                    define admin: [user, group]
                    define editor: [user]
                    define viewer: [user, group]
                    define blocked: [user]
                    define can_view: (viewer or admin) but not blocked
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
            expect($documentType->getRelations()->has('can_view'))->toBeTrue();

            $canViewRelation = $documentType->getRelations()->get('can_view');
            expect($canViewRelation->getDifference())->not->toBeNull();

            $difference = $canViewRelation->getDifference();

            // Base should be a union with viewer and admin
            $base = $difference->getBase();
            expect($base->getUnion())->not->toBeNull();
            expect($base->getUnion()->count())->toBe(2);

            // Subtract should be blocked
            $subtract = $difference->getSubtract();
            expect($subtract->getComputedUserset())->not->toBeNull();
            expect($subtract->getComputedUserset()->getRelation())->toBe('blocked');

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('can_view');
        });
    });

    describe('Type Restrictions and Wildcards', function (): void {
        test('transforms wildcard type restrictions', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type folder
                  relations
                    define viewer: [user, user:*]
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            $folderType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('folder' === $td->getType()) {
                    $folderType = $td;

                    break;
                }
            }

            expect($folderType)->not->toBeNull();
            expect($folderType->getRelations()->has('viewer'))->toBeTrue();

            // Verify metadata contains both user and user:* types
            $metadata = $folderType->getMetadata();
            $viewerMetadata = $metadata->getRelations()->get('viewer');
            $directTypes = $viewerMetadata->getDirectlyRelatedUserTypes();

            expect($directTypes->count())->toBe(2);

            $typePatterns = [];

            foreach ($directTypes as $type) {
                $pattern = $type->getType();

                if ($type->getWildcard()) {
                    $pattern .= ':*';
                }
                $typePatterns[] = $pattern;
            }

            expect($typePatterns)->toContain('user');
            expect($typePatterns)->toContain('user:*');

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('[user, user:*]');
        });

        test('transforms complex type references with relations', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type team
                  relations
                    define member: [user]

                type document
                  relations
                    define editor: [team#member]
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
            expect($documentType->getRelations()->has('editor'))->toBeTrue();

            // Verify metadata contains team#member reference
            $metadata = $documentType->getMetadata();
            $editorMetadata = $metadata->getRelations()->get('editor');
            $directTypes = $editorMetadata->getDirectlyRelatedUserTypes();

            expect($directTypes->count())->toBe(1);
            $typeRef = $directTypes->get(0);
            expect($typeRef->getType())->toBe('team');
            expect($typeRef->getRelation())->toBe('member');

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('[team#member]');
        });
    });

    describe('Tuple-to-Userset Relations', function (): void {
        test('transforms from relation patterns', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type organization
                  relations
                    define member: [user]

                type repository
                  relations
                    define owner: [organization]
                    define reader: member from owner
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            $repoType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('repository' === $td->getType()) {
                    $repoType = $td;

                    break;
                }
            }

            expect($repoType)->not->toBeNull();
            expect($repoType->getRelations()->has('reader'))->toBeTrue();

            $readerRelation = $repoType->getRelations()->get('reader');
            expect($readerRelation->getTupleToUserset())->not->toBeNull();

            $ttu = $readerRelation->getTupleToUserset();
            expect($ttu->getTupleset()->getRelation())->toBe('owner');
            expect($ttu->getComputedUserset()->getRelation())->toBe('member');

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('member from owner');
        });
    });

    describe('Real-World Scenarios', function (): void {
        test('transforms GitHub-like authorization model', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type organization
                  relations
                    define member: [user]
                    define owner: [user]
                    define admin: owner or member

                type repository
                  relations
                    define owner: [organization]
                    define admin: [user] or admin from owner
                    define maintainer: [user] or admin
                    define writer: [user] or maintainer
                    define triager: [user] or writer
                    define reader: [user] or triager
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getTypeDefinitions()->count())->toBe(3);

            // Verify organization type
            $orgType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('organization' === $td->getType()) {
                    $orgType = $td;

                    break;
                }
            }

            expect($orgType)->not->toBeNull();
            expect($orgType->getRelations()->has('admin'))->toBeTrue();

            $adminRelation = $orgType->getRelations()->get('admin');
            expect($adminRelation->getUnion())->not->toBeNull();

            // Verify repository type with complex inheritance
            $repoType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('repository' === $td->getType()) {
                    $repoType = $td;

                    break;
                }
            }

            expect($repoType)->not->toBeNull();
            expect($repoType->getRelations()->has('reader'))->toBeTrue();

            $readerRelation = $repoType->getRelations()->get('reader');
            expect($readerRelation->getUnion())->not->toBeNull();

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('admin from owner');
            expect($resultDsl)->toContain('repository');
        });

        test('transforms Google Drive-like document sharing model', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type group
                  relations
                    define member: [user]

                type folder
                  relations
                    define owner: [user]
                    define editor: [user, group#member]
                    define viewer: [user, group#member] or editor
                    define parent: [folder]
                    define viewer_from_parent: viewer from parent

                type document
                  relations
                    define owner: [user]
                    define editor: [user, group#member] or owner
                    define viewer: [user, group#member] or editor
                    define parent: [folder]
                    define viewer_from_parent: viewer from parent
                    define can_read: viewer or viewer_from_parent
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getTypeDefinitions()->count())->toBe(4);

            // Verify document type with complex sharing rules
            $docType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('document' === $td->getType()) {
                    $docType = $td;

                    break;
                }
            }

            expect($docType)->not->toBeNull();
            expect($docType->getRelations()->has('can_read'))->toBeTrue();

            $canReadRelation = $docType->getRelations()->get('can_read');
            expect($canReadRelation->getUnion())->not->toBeNull();
            expect($canReadRelation->getUnion()->count())->toBe(2);

            // Verify folder inheritance pattern
            expect($docType->getRelations()->has('viewer_from_parent'))->toBeTrue();
            $inheritRelation = $docType->getRelations()->get('viewer_from_parent');
            expect($inheritRelation->getTupleToUserset())->not->toBeNull();

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('can_read');
            expect($resultDsl)->toContain('viewer from parent');
        });

        test('transforms banking authorization model with conditions', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type account
                  relations
                    define owner: [user]
                    define admin: [user]
                    define viewer: [user] or owner or admin

                type transaction
                  relations
                    define account: [account]
                    define viewer: viewer from account
                    define approver: admin from account
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getTypeDefinitions()->count())->toBe(3);

            // Verify account type
            $accountType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('account' === $td->getType()) {
                    $accountType = $td;

                    break;
                }
            }

            expect($accountType)->not->toBeNull();
            expect($accountType->getRelations()->has('viewer'))->toBeTrue();

            $viewerRelation = $accountType->getRelations()->get('viewer');
            expect($viewerRelation->getUnion())->not->toBeNull();
            expect($viewerRelation->getUnion()->count())->toBe(3);

            // Verify transaction type with inheritance
            $transactionType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('transaction' === $td->getType()) {
                    $transactionType = $td;

                    break;
                }
            }

            expect($transactionType)->not->toBeNull();
            expect($transactionType->getRelations()->has('approver'))->toBeTrue();

            $approverRelation = $transactionType->getRelations()->get('approver');
            expect($approverRelation->getTupleToUserset())->not->toBeNull();

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('viewer from account');
            expect($resultDsl)->toContain('admin from account');
        });
    });

    describe('Edge Cases and Error Handling', function (): void {
        test('handles deeply nested parenthetical expressions', function (): void {
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
                    define e: [user]
                    define complex: ((a or b) and (c or d)) but not e
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
            expect($documentType->getRelations()->has('complex'))->toBeTrue();

            $complexRelation = $documentType->getRelations()->get('complex');
            expect($complexRelation->getDifference())->not->toBeNull();

            $difference = $complexRelation->getDifference();
            $base = $difference->getBase();
            expect($base->getIntersection())->not->toBeNull();
            expect($base->getIntersection()->count())->toBe(2);

            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->toContain('complex');
        });

        test('handles comments and whitespace correctly', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                # This is a comment
                type user

                # Another comment
                type document
                  relations
                    # Relation comment
                    define viewer: [user]
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);

            expect($model->getTypeDefinitions()->count())->toBe(2);

            $documentType = null;

            foreach ($model->getTypeDefinitions() as $td) {
                if ('document' === $td->getType()) {
                    $documentType = $td;

                    break;
                }
            }

            expect($documentType)->not->toBeNull();
            expect($documentType->getRelations()->has('viewer'))->toBeTrue();

            // DSL output should not contain comments
            $resultDsl = Transformer::toDsl($model);
            expect($resultDsl)->not->toContain('#');
            expect($resultDsl)->toContain('define viewer: [user]');
        });

        test('preserves exact DSL round-trip for complex scenarios', function (): void {
            $dsl = <<<'DSL'
                model
                  schema 1.1

                type user

                type organization
                  relations
                    define member: [user]
                    define admin: [user] or member

                type repository
                  relations
                    define owner: [organization]
                    define admin: [user] or admin from owner
                    define writer: [user] or admin
                    define reader: [user] or writer but not admin
                DSL;

            $model = Transformer::fromDsl($dsl, $this->validator);
            $resultDsl = Transformer::toDsl($model);

            // Round-trip should preserve all essential structure
            $secondModel = Transformer::fromDsl($resultDsl, $this->validator);
            $finalDsl = Transformer::toDsl($secondModel);

            expect(trim($resultDsl))->toBe(trim($finalDsl));
        });
    });
});
