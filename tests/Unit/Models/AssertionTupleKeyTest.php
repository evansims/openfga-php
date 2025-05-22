<?php

namespace OpenFGATests\Unit\Models;

use OpenFGA\Models\AssertionTupleKey;
use OpenFGA\Schema\SchemaInterface;

describe('AssertionTupleKey', function () {
    describe('constructor', function () {
        it('constructs with user, relation, and object', function () {
            $tupleKey = new AssertionTupleKey(
                user: 'user:anne',
                relation: 'viewer',
                object: 'document:123'
            );
            expect($tupleKey->getUser())->toBe('user:anne')
                ->and($tupleKey->getRelation())->toBe('viewer')
                ->and($tupleKey->getObject())->toBe('document:123');
        });
    });

    describe('getters', function () {
        $tupleKey = new AssertionTupleKey('u1', 'r1', 'o1');

        it('getUser returns the correct value', function () use ($tupleKey) {
            expect($tupleKey->getUser())->toBe('u1');
        });

        it('getRelation returns the correct value', function () use ($tupleKey) {
            expect($tupleKey->getRelation())->toBe('r1');
        });

        it('getObject returns the correct value', function () use ($tupleKey) {
            expect($tupleKey->getObject())->toBe('o1');
        });
    });

    describe('jsonSerialize', function () {
        it('serializes to an array with user, relation, and object', function () {
            $tupleKey = new AssertionTupleKey(
                user: 'user:bob',
                relation: 'editor',
                object: 'folder:abc'
            );
            expect($tupleKey->jsonSerialize())->toBe([
                'user' => 'user:bob',
                'relation' => 'editor',
                'object' => 'folder:abc',
            ]);
        });
    });

    describe('static schema()', function () {
        $schema = AssertionTupleKey::schema();

        it('returns a SchemaInterface instance', function () use ($schema) {
            expect($schema)->toBeInstanceOf(SchemaInterface::class);
        });

        it('has the correct className', function () use ($schema) {
            expect($schema->getClassName())->toBe(AssertionTupleKey::class);
        });

        it('has "user" property defined correctly as required string', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('user');
            $prop = $properties['user'];
            expect($prop->getName())->toBe('user')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "relation" property defined correctly as required string', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('relation');
            $prop = $properties['relation'];
            expect($prop->getName())->toBe('relation')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });

        it('has "object" property defined correctly as required string', function () use ($schema) {
            $properties = $schema->getProperties();
            expect($properties)->toHaveKey('object');
            $prop = $properties['object'];
            expect($prop->getName())->toBe('object')
                ->and($prop->getTypes())->toBe(['string'])
                ->and($prop->isRequired())->toBeTrue();
        });
    });
});

?>
