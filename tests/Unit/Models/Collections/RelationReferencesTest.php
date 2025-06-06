<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{RelationReferences, RelationReferencesInterface};
use OpenFGA\Models\{RelationReference, TypedWildcard};
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('RelationReferences Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new RelationReferences([]);

        expect($collection)->toBeInstanceOf(RelationReferencesInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new RelationReferences([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of relation references', function (): void {
        $ref1 = new RelationReference(
            type: 'document',
            relation: 'viewer',
        );
        $ref2 = new RelationReference(
            type: 'folder',
            relation: 'editor',
        );
        $ref3 = new RelationReference(
            type: 'organization',
            relation: 'member',
        );

        $collection = new RelationReferences([
            $ref1,
            $ref2,
            $ref3,
        ]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds relation references', function (): void {
        $collection = new RelationReferences([]);
        $ref = new RelationReference(type: 'document', relation: 'viewer');

        $collection->add($ref);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($ref);
    });

    test('gets relation references by index', function (): void {
        $ref1 = new RelationReference(type: 'document', relation: 'viewer');
        $ref2 = new RelationReference(type: 'folder', relation: 'editor');
        $ref3 = new RelationReference(type: 'organization', relation: 'member');

        $collection = new RelationReferences([$ref1, $ref2, $ref3]);

        expect($collection->get(0)->getType())->toBe('document');
        expect($collection->get(1)->getType())->toBe('folder');
        expect($collection->get(2)->getType())->toBe('organization');
    });

    test('checks if reference exists by index', function (): void {
        $ref = new RelationReference(type: 'group', relation: 'member');
        $collection = new RelationReferences([$ref]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over references', function (): void {
        $ref1 = new RelationReference(type: 'document', relation: 'viewer');
        $ref2 = new RelationReference(type: 'folder', relation: 'editor');
        $ref3 = new RelationReference(type: 'organization', relation: 'member');

        $collection = new RelationReferences([$ref1, $ref2, $ref3]);

        $types = [];

        foreach ($collection as $index => $ref) {
            $types[] = $ref->getType();
            expect($index)->toBeInt();
        }

        expect($types)->toBe(['document', 'folder', 'organization']);
    });

    test('toArray', function (): void {
        $ref1 = new RelationReference(type: 'document', relation: 'viewer');
        $ref2 = new RelationReference(type: 'folder', relation: 'editor');

        $collection = new RelationReferences([$ref1, $ref2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($ref1);
        expect($array[1])->toBe($ref2);
    });

    test('jsonSerialize', function (): void {
        $collection = new RelationReferences([
            new RelationReference(type: 'user'),
            new RelationReference(type: 'group', relation: 'member'),
            new RelationReference(
                type: 'user',
                wildcard: new TypedWildcard(type: 'wildcard'),
            ),
            new RelationReference(
                type: 'org',
                relation: 'admin',
                condition: 'condition1',
            ),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(4);

        // First reference - simple type
        expect($json[0])->toBe(['type' => 'user']);

        // Second reference - with relation
        expect($json[1])->toBe([
            'type' => 'group',
            'relation' => 'member',
        ]);

        // Third reference - with wildcard
        expect($json[2])->toHaveKey('type');
        expect($json[2])->toHaveKey('wildcard');
        // The wildcard is not serialized, it remains as the TypedWildcard object
        expect($json[2]['wildcard'])->toBeInstanceOf(TypedWildcard::class);
        expect($json[2]['wildcard']->getType())->toBe('wildcard');

        // Fourth reference - with condition (should be omitted if empty)
        expect($json[3])->toBe([
            'type' => 'org',
            'relation' => 'admin',
            'condition' => 'condition1',
        ]);
    });

    test('handles references with wildcard', function (): void {
        $wildcard = new TypedWildcard(type: 'user');
        $ref = new RelationReference(
            type: 'group',
            wildcard: $wildcard,
        );

        $collection = new RelationReferences([$ref]);

        expect($collection->count())->toBe(1);
        $retrieved = $collection->get(0);
        expect($retrieved->getType())->toBe('group');
        expect($retrieved->getWildcard())->toBe($wildcard);
    });

    test('handles references with all properties', function (): void {
        $wildcard = new TypedWildcard(type: 'user');
        $ref = new RelationReference(
            type: 'group',
            relation: 'admin',
            wildcard: $wildcard,
            condition: 'inTenant',
        );

        $collection = new RelationReferences([$ref]);
        $retrieved = $collection->get(0);

        expect($retrieved->getType())->toBe('group');
        expect($retrieved->getRelation())->toBe('admin');
        expect($retrieved->getWildcard())->toBe($wildcard);
        expect($retrieved->getCondition())->toBe('inTenant');
    });

    test('schema', function (): void {
        $schema = RelationReferences::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(RelationReferences::class);
    });

    test('schema is cached', function (): void {
        $schema1 = RelationReferences::schema();
        $schema2 = RelationReferences::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema1)->toBe($schema2);
    });

    test('handles edge cases', function (): void {
        $collection = new RelationReferences([]);

        // Empty collection
        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Access non-existent index
        expect($collection->get(0))->toBeNull();
        expect($collection->get(999))->toBeNull();

        // Iteration on empty
        $count = 0;

        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);
    });

    test('ensures empty conditions are not serialized', function (): void {
        $ref = new RelationReference(
            type: 'user',
            relation: 'member',
            condition: '', // Empty condition
        );

        $collection = new RelationReferences([$ref]);
        $json = $collection->jsonSerialize();

        // Empty condition should be omitted
        expect($json[0])->toBe([
            'type' => 'user',
            'relation' => 'member',
        ]);
        expect($json[0])->not->toHaveKey('condition');
    });
});
