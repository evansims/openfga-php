<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{TypeDefinitionRelations, TypeDefinitionRelationsInterface, Usersets};
use OpenFGA\Models\{DifferenceV1, ObjectRelation, TupleToUsersetV1, Userset};
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};
use stdClass;

describe('TypeDefinitionRelations Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new TypeDefinitionRelations([]);

        expect($collection)->toBeInstanceOf(TypeDefinitionRelationsInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new TypeDefinitionRelations([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of relations', function (): void {
        $relations = [
            'viewer' => new Userset(
                direct: new stdClass,
            ),
            'editor' => new Userset(
                direct: new stdClass,
                computedUserset: new ObjectRelation(relation: 'viewer'),
            ),
            'owner' => new Userset(
                direct: new stdClass,
            ),
        ];

        $collection = new TypeDefinitionRelations($relations);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('gets relations by key', function (): void {
        $viewerRelation = new Userset(direct: new stdClass);
        $editorRelation = new Userset(direct: new stdClass);

        $collection = new TypeDefinitionRelations([
            'viewer' => $viewerRelation,
            'editor' => $editorRelation,
        ]);

        expect($collection->get('viewer'))->toBe($viewerRelation);
        expect($collection->get('editor'))->toBe($editorRelation);
        expect($collection->get('nonexistent'))->toBeNull();
    });

    test('iterates over relations', function (): void {
        $relations = [
            'read' => new Userset(direct: new stdClass),
            'write' => new Userset(direct: new stdClass),
            'admin' => new Userset(direct: new stdClass),
        ];

        $collection = new TypeDefinitionRelations($relations);

        $keys = [];

        foreach ($collection as $key => $relation) {
            $keys[] = $key;
            expect($relation)->toBeInstanceOf(Userset::class);
        }

        expect($keys)->toBe(['read', 'write', 'admin']);
    });

    test('toArray', function (): void {
        $viewerRelation = new Userset(direct: new stdClass);
        $editorRelation = new Userset(direct: new stdClass);

        $collection = new TypeDefinitionRelations([
            'viewer' => $viewerRelation,
            'editor' => $editorRelation,
        ]);

        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array['viewer'])->toBe($viewerRelation);
        expect($array['editor'])->toBe($editorRelation);
    });

    test('jsonSerialize', function (): void {
        $collection = new TypeDefinitionRelations([
            'viewer' => new Userset(
                direct: new stdClass,
            ),
            'editor' => new Userset(
                union: new Usersets([
                    new Userset(direct: new stdClass),
                    new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
                ]),
            ),
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveKey('viewer');
        expect($json)->toHaveKey('editor');

        // Check viewer structure - 'direct' is serialized as 'this'
        expect($json['viewer'])->toHaveKey('this');
        expect($json['viewer']['this'])->toBeInstanceOf(stdClass::class);

        // Check editor structure - union now uses {child: [...]} format
        expect($json['editor'])->toHaveKey('union');
        expect($json['editor']['union'])->toHaveKey('child');
        expect($json['editor']['union']['child'])->toBeArray();
        expect($json['editor']['union']['child'])->toHaveCount(2);
    });

    test('checks if key exists', function (): void {
        $collection = new TypeDefinitionRelations([
            'viewer' => new Userset(direct: new stdClass),
            'editor' => new Userset(direct: new stdClass),
        ]);

        expect(isset($collection['viewer']))->toBeTrue();
        expect(isset($collection['editor']))->toBeTrue();
        expect(isset($collection['owner']))->toBeFalse();
    });

    test('returns all keys', function (): void {
        $collection = new TypeDefinitionRelations([
            'read' => new Userset(direct: new stdClass),
            'write' => new Userset(direct: new stdClass),
            'delete' => new Userset(direct: new stdClass),
        ]);

        $keys = array_keys($collection->toArray());

        expect($keys)->toBeArray();
        expect($keys)->toBe(['read', 'write', 'delete']);
    });

    test('handles complex relation definitions', function (): void {
        $collection = new TypeDefinitionRelations([
            // Direct assignment
            'viewer' => new Userset(direct: new stdClass),

            // Union of direct and computed
            'editor' => new Userset(
                union: new Usersets([
                    new Userset(direct: new stdClass),
                    new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
                ]),
            ),

            // Intersection
            'reviewer' => new Userset(
                intersection: new Usersets([
                    new Userset(computedUserset: new ObjectRelation(relation: 'editor')),
                    new Userset(tupleToUserset: new TupleToUsersetV1(
                        tupleset: new ObjectRelation(relation: 'assigned_reviewers'),
                        computedUserset: new ObjectRelation(relation: 'member'),
                    )),
                ]),
            ),

            // Difference
            'commenter' => new Userset(
                difference: new DifferenceV1(
                    base: new Userset(computedUserset: new ObjectRelation(relation: 'viewer')),
                    subtract: new Userset(computedUserset: new ObjectRelation(relation: 'blocked')),
                ),
            ),
        ]);

        expect($collection->count())->toBe(4);
        expect(isset($collection['viewer']))->toBeTrue();
        expect(isset($collection['editor']))->toBeTrue();
        expect(isset($collection['reviewer']))->toBeTrue();
        expect(isset($collection['commenter']))->toBeTrue();
    });

    test('schema', function (): void {
        $schema = TypeDefinitionRelations::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(TypeDefinitionRelations::class);
    });

    test('schema is cached', function (): void {
        $schema1 = TypeDefinitionRelations::schema();
        $schema2 = TypeDefinitionRelations::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema1)->toBe($schema2);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new TypeDefinitionRelations([]);

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);
        expect(array_keys($collection->toArray()))->toBe([]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $_) {
            ++$count;
        }
        expect($count)->toBe(0);

        // Test get on empty collection
        expect($collection->get('any'))->toBeNull();
        expect(isset($collection['any']))->toBeFalse();
    });
});
