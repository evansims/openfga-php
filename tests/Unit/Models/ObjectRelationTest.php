<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\ObjectRelation;
use OpenFGA\Schema\SchemaInterface;

describe('ObjectRelation', function () {
    describe('constructor', function () {
        it('constructs with all parameters', function () {
            $objectRelation = new ObjectRelation(object: 'document:123', relation: 'viewer');
            expect($objectRelation->getObject())->toBe('document:123')
                ->and($objectRelation->getRelation())->toBe('viewer');
        });

        it('constructs with only object', function () {
            $objectRelation = new ObjectRelation(object: 'document:123');
            expect($objectRelation->getObject())->toBe('document:123')
                ->and($objectRelation->getRelation())->toBeNull();
        });

        it('constructs with only relation', function () {
            $objectRelation = new ObjectRelation(relation: 'viewer');
            expect($objectRelation->getObject())->toBeNull()
                ->and($objectRelation->getRelation())->toBe('viewer');
        });

        it('constructs with no parameters', function () {
            $objectRelation = new ObjectRelation();
            expect($objectRelation->getObject())->toBeNull()
                ->and($objectRelation->getRelation())->toBeNull();
        });
    });

    describe('getters', function () {
        it('getObject returns the correct value', function () {
            $objectRelation = new ObjectRelation(object: 'user:anne');
            expect($objectRelation->getObject())->toBe('user:anne');

            $objectRelationNull = new ObjectRelation();
            expect($objectRelationNull->getObject())->toBeNull();
        });

        it('getRelation returns the correct value', function () {
            $objectRelation = new ObjectRelation(relation: 'editor');
            expect($objectRelation->getRelation())->toBe('editor');

            $objectRelationNull = new ObjectRelation();
            expect($objectRelationNull->getRelation())->toBeNull();
        });
    });

    describe('jsonSerialize', function () {
        it('serializes with all properties set', function () {
            $objectRelation = new ObjectRelation(object: 'document:budget', relation: 'reader');
            expect($objectRelation->jsonSerialize())->toBe([
                'object' => 'document:budget',
                'relation' => 'reader',
            ]);
        });

        it('serializes with only object set', function () {
            $objectRelation = new ObjectRelation(object: 'document:budget');
            expect($objectRelation->jsonSerialize())->toBe([
                'object' => 'document:budget',
            ]);
        });

        it('serializes with only relation set', function () {
            $objectRelation = new ObjectRelation(relation: 'reader');
            expect($objectRelation->jsonSerialize())->toBe([
                'relation' => 'reader',
            ]);
        });

        it('serializes to an empty array when all properties are null', function () {
            $objectRelation = new ObjectRelation();
            expect($objectRelation->jsonSerialize())->toBe([]);
        });
    });

    describe('schema', function () {
        $schema = ObjectRelation::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(ObjectRelation::class);
        });

        it('has the "object" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('object');
            $objectProp = $properties['object'];
            expect($objectProp->getName())->toBe('object')
                ->and($objectProp->getTypes())->toBe(['string'])
                ->and($objectProp->isRequired())->toBeFalse();
        });

        it('has the "relation" property defined correctly', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('relation');
            $relationProp = $properties['relation'];
            expect($relationProp->getName())->toBe('relation')
                ->and($relationProp->getTypes())->toBe(['string'])
                ->and($relationProp->isRequired())->toBeFalse();
        });
    });
});

?>
