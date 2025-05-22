<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\RelationReference;
use OpenFGA\Schema\SchemaInterface;
use stdClass;

describe('RelationReference', function () {
    describe('constructor', function () {
        it('constructs with only type', function () {
            $ref = new RelationReference(type: 'user');
            expect($ref->getType())->toBe('user')
                ->and($ref->getRelation())->toBeNull()
                ->and($ref->getWildcard())->toBeNull()
                ->and($ref->getCondition())->toBeNull();
        });

        it('constructs with type and relation', function () {
            $ref = new RelationReference(type: 'document', relation: 'viewer');
            expect($ref->getType())->toBe('document')
                ->and($ref->getRelation())->toBe('viewer')
                ->and($ref->getWildcard())->toBeNull()
                ->and($ref->getCondition())->toBeNull();
        });

        it('constructs with type and wildcard', function () {
            $wildcardObj = new stdClass();
            $ref = new RelationReference(type: 'folder', wildcard: $wildcardObj);
            expect($ref->getType())->toBe('folder')
                ->and($ref->getRelation())->toBeNull()
                ->and($ref->getWildcard())->toBe($wildcardObj)
                ->and($ref->getCondition())->toBeNull();
        });

        it('constructs with type and condition name', function () {
            $ref = new RelationReference(type: 'group', condition: 'is_member');
            expect($ref->getType())->toBe('group')
                ->and($ref->getRelation())->toBeNull()
                ->and($ref->getWildcard())->toBeNull()
                ->and($ref->getCondition())->toBe('is_member');
        });

        it('constructs with all parameters', function () {
            $wildcardObj = new stdClass();
            $ref = new RelationReference(
                type: 'organization',
                relation: 'admin',
                wildcard: $wildcardObj,
                condition: 'has_role'
            );
            expect($ref->getType())->toBe('organization')
                ->and($ref->getRelation())->toBe('admin')
                ->and($ref->getWildcard())->toBe($wildcardObj)
                ->and($ref->getCondition())->toBe('has_role');
        });
    });

    describe('getters', function () {
        $wildcardObj = new stdClass();
        $refWithAll = new RelationReference('test_type', 'test_relation', $wildcardObj, 'test_condition');
        $refOnlyType = new RelationReference('only_type');

        it('getType returns the correct value', function () use ($refWithAll, $refOnlyType) {
            expect($refWithAll->getType())->toBe('test_type')
                ->and($refOnlyType->getType())->toBe('only_type');
        });

        it('getRelation returns the correct value or null', function () use ($refWithAll, $refOnlyType) {
            expect($refWithAll->getRelation())->toBe('test_relation')
                ->and($refOnlyType->getRelation())->toBeNull();
        });

        it('getWildcard returns the correct value or null', function () use ($refWithAll, $wildcardObj, $refOnlyType) {
            expect($refWithAll->getWildcard())->toBe($wildcardObj)
                ->and($refOnlyType->getWildcard())->toBeNull();
        });

        it('getCondition returns the correct value or null', function () use ($refWithAll, $refOnlyType) {
            expect($refWithAll->getCondition())->toBe('test_condition')
                ->and($refOnlyType->getCondition())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with only type set', function () {
            $ref = new RelationReference(type: 'user');
            expect($ref->jsonSerialize())->toBe(['type' => 'user']);
        });

        it('serializes with all parameters set', function () {
            $wildcardObj = new stdClass(); // stdClass serializes to an empty object {}
            $ref = new RelationReference(
                type: 'document',
                relation: 'editor',
                wildcard: $wildcardObj,
                condition: 'is_public'
            );
            expect($ref->jsonSerialize())->toBe([
                'type' => 'document',
                'relation' => 'editor',
                'wildcard' => $wildcardObj, // In PHP, stdClass will be {}
                'condition' => 'is_public',
            ]);
        });

        it('serializes with some optional parameters null', function () {
            $ref = new RelationReference(type: 'folder', relation: 'parent');
            expect($ref->jsonSerialize())->toBe([
                'type' => 'folder',
                'relation' => 'parent',
            ]); // Wildcard and condition are null, so they should be excluded by array_filter

            $wildcardObj = new stdClass();
            $refWithWildcard = new RelationReference(type: 'image', wildcard: $wildcardObj);
            expect($refWithWildcard->jsonSerialize())->toBe([
                'type' => 'image',
                'wildcard' => $wildcardObj,
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = RelationReference::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(RelationReference::class);
        });

        it('has "type" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('type');
            $prop = $properties['type'];
            expect($prop->getName())->toBe('type')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "relation" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('relation');
            $prop = $properties['relation'];
            expect($prop->getName())->toBe('relation')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "wildcard" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('wildcard');
            $prop = $properties['wildcard'];
            expect($prop->getName())->toBe('wildcard')
                ->and($prop->getTypes())->toBe(['object'])
                ->and($prop->isRequired())->toBeFalse();
        });

        it('has "condition" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('condition');
            $prop = $properties['condition'];
            expect($prop->getName())->toBe('condition')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeFalse();
        });
    });
});

?>
