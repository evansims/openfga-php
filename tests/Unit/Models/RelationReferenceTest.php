<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{RelationReference, RelationReferenceInterface};
use OpenFGA\Schemas\SchemaInterface;
use stdClass;

describe('RelationReference Model', function (): void {
    test('implements RelationReferenceInterface', function (): void {
        $relationReference = new RelationReference(type: 'user');

        expect($relationReference)->toBeInstanceOf(RelationReferenceInterface::class);
    });

    test('constructs with type only', function (): void {
        $relationReference = new RelationReference(type: 'user');

        expect($relationReference->getType())->toBe('user');
        expect($relationReference->getRelation())->toBeNull();
        expect($relationReference->getWildcard())->toBeNull();
        expect($relationReference->getCondition())->toBeNull();
    });

    test('constructs with type and relation', function (): void {
        $relationReference = new RelationReference(
            type: 'group',
            relation: 'member',
        );

        expect($relationReference->getType())->toBe('group');
        expect($relationReference->getRelation())->toBe('member');
        expect($relationReference->getWildcard())->toBeNull();
        expect($relationReference->getCondition())->toBeNull();
    });

    test('constructs with type and wildcard', function (): void {
        $wildcard = new stdClass;
        $relationReference = new RelationReference(
            type: 'user',
            wildcard: $wildcard,
        );

        expect($relationReference->getType())->toBe('user');
        expect($relationReference->getRelation())->toBeNull();
        expect($relationReference->getWildcard())->toBe($wildcard);
        expect($relationReference->getCondition())->toBeNull();
    });

    test('constructs with type and condition', function (): void {
        $relationReference = new RelationReference(
            type: 'user',
            condition: 'inRegion',
        );

        expect($relationReference->getType())->toBe('user');
        expect($relationReference->getRelation())->toBeNull();
        expect($relationReference->getWildcard())->toBeNull();
        expect($relationReference->getCondition())->toBe('inRegion');
    });

    test('constructs with all parameters', function (): void {
        $wildcard = new stdClass;
        $relationReference = new RelationReference(
            type: 'group',
            relation: 'member',
            wildcard: $wildcard,
            condition: 'isActive',
        );

        expect($relationReference->getType())->toBe('group');
        expect($relationReference->getRelation())->toBe('member');
        expect($relationReference->getWildcard())->toBe($wildcard);
        expect($relationReference->getCondition())->toBe('isActive');
    });

    test('handles various type names', function (): void {
        $types = [
            'user',
            'group',
            'document',
            'folder',
            'organization',
            'team',
            'project',
            'type-with-dash',
            'type_with_underscore',
        ];

        foreach ($types as $type) {
            $relationReference = new RelationReference(type: $type);
            expect($relationReference->getType())->toBe($type);
        }
    });

    test('serializes to JSON with only non-null fields', function (): void {
        $relationReference = new RelationReference(type: 'user');
        expect($relationReference->jsonSerialize())->toBe(['type' => 'user']);

        $relationReference = new RelationReference(
            type: 'group',
            relation: 'member',
        );
        expect($relationReference->jsonSerialize())->toBe([
            'type' => 'group',
            'relation' => 'member',
        ]);
    });

    test('serializes to JSON with wildcard', function (): void {
        $wildcard = new stdClass;
        $relationReference = new RelationReference(
            type: 'user',
            wildcard: $wildcard,
        );

        $json = $relationReference->jsonSerialize();
        expect($json)->toHaveKeys(['type', 'wildcard']);
        expect($json['type'])->toBe('user');
        expect($json['wildcard'])->toBe($wildcard);
    });

    test('serializes to JSON with all fields', function (): void {
        $wildcard = new stdClass;
        $relationReference = new RelationReference(
            type: 'group',
            relation: 'member',
            wildcard: $wildcard,
            condition: 'isActive',
        );

        $json = $relationReference->jsonSerialize();
        expect($json)->toHaveKeys(['type', 'relation', 'wildcard', 'condition']);
        expect($json['type'])->toBe('group');
        expect($json['relation'])->toBe('member');
        expect($json['wildcard'])->toBe($wildcard);
        expect($json['condition'])->toBe('isActive');
    });

    test('returns schema instance', function (): void {
        $schema = RelationReference::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(RelationReference::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(4);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['type', 'relation', 'wildcard', 'condition']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = RelationReference::schema();
        $properties = $schema->getProperties();

        // Type property
        $typeProp = $properties['type'];
        expect($typeProp->name)->toBe('type');
        expect($typeProp->type)->toBe('string');
        expect($typeProp->required)->toBe(true);

        // Relation property
        $relationProp = $properties['relation'];
        expect($relationProp->name)->toBe('relation');
        expect($relationProp->type)->toBe('string');
        expect($relationProp->required)->toBe(false);

        // Wildcard property
        $wildcardProp = $properties['wildcard'];
        expect($wildcardProp->name)->toBe('wildcard');
        expect($wildcardProp->type)->toBe('object');
        expect($wildcardProp->required)->toBe(false);

        // Condition property
        $conditionProp = $properties['condition'];
        expect($conditionProp->name)->toBe('condition');
        expect($conditionProp->type)->toBe('string');
        expect($conditionProp->required)->toBe(false);
    });

    test('schema is cached', function (): void {
        $schema1 = RelationReference::schema();
        $schema2 = RelationReference::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves empty strings', function (): void {
        $relationReference = new RelationReference(
            type: '',
            relation: '',
            condition: '',
        );

        expect($relationReference->getType())->toBe('');
        expect($relationReference->getRelation())->toBe('');
        expect($relationReference->getCondition())->toBe('');
    });

    test('preserves whitespace', function (): void {
        $relationReference = new RelationReference(
            type: '  user  ',
            relation: '  member  ',
            condition: '  isActive  ',
        );

        expect($relationReference->getType())->toBe('  user  ');
        expect($relationReference->getRelation())->toBe('  member  ');
        expect($relationReference->getCondition())->toBe('  isActive  ');
    });
});
