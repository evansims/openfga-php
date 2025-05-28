<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\{TypeDefinitionRelations, TypeDefinitions, TypeDefinitionsInterface};
use OpenFGA\Models\{TypeDefinition};
use OpenFGA\Schema\CollectionSchemaInterface;

describe('TypeDefinitions Collection', function (): void {
    test('implements TypeDefinitionsInterface', function (): void {
        $collection = new TypeDefinitions();

        expect($collection)->toBeInstanceOf(TypeDefinitionsInterface::class);
    });

    test('creates empty collection', function (): void {
        $collection = new TypeDefinitions();

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
        expect($collection->toArray())->toBe([]);
    });

    test('creates collection with single type definition', function (): void {
        $typeDefinition = new TypeDefinition(type: 'user');
        $collection = new TypeDefinitions([$typeDefinition]);

        expect($collection->count())->toBe(1);
        expect($collection->isEmpty())->toBe(false);
        expect($collection->get(0))->toBe($typeDefinition);
    });

    test('creates collection with multiple type definitions', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(type: 'document');
        $folderType = new TypeDefinition(type: 'folder');

        $collection = new TypeDefinitions([$userType, $documentType, $folderType]);

        expect($collection->count())->toBe(3);
        expect($collection->get(0))->toBe($userType);
        expect($collection->get(1))->toBe($documentType);
        expect($collection->get(2))->toBe($folderType);
    });

    test('adds type definitions to collection', function (): void {
        $collection = new TypeDefinitions();

        $userType = new TypeDefinition(type: 'user');
        $collection->add($userType);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($userType);

        $documentType = new TypeDefinition(type: 'document');
        $collection->add($documentType);

        expect($collection->count())->toBe(2);
        expect($collection->get(1))->toBe($documentType);
    });

    test('iterates over collection', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(type: 'document');
        $collection = new TypeDefinitions([$userType, $documentType]);

        $types = [];
        foreach ($collection as $index => $type) {
            $types[$index] = $type;
        }

        expect($types)->toBe([0 => $userType, 1 => $documentType]);
    });

    test('accesses type definitions by array notation', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(type: 'document');
        $collection = new TypeDefinitions([$userType, $documentType]);

        expect($collection[0])->toBe($userType);
        expect($collection[1])->toBe($documentType);
        expect(isset($collection[0]))->toBe(true);
        expect(isset($collection[2]))->toBe(false);
    });

    test('returns null for non-existent index', function (): void {
        $typeDefinition = new TypeDefinition(type: 'user');
        $collection = new TypeDefinitions([$typeDefinition]);

        expect($collection->get(1))->toBeNull();
        expect($collection[999])->toBeNull();
    });

    test('serializes to JSON', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(
            type: 'document',
            relations: new TypeDefinitionRelations([]),
        );
        $collection = new TypeDefinitions([$userType, $documentType]);

        $json = $collection->jsonSerialize();

        expect($json)->toHaveCount(2);

        expect($json[0]['type'])->toBe('user');
        expect($json[0]['relations'])->toBeInstanceOf(stdClass::class);
        expect($json[0])->not->toHaveKey('metadata');

        expect($json[1]['type'])->toBe('document');
        expect($json[1]['relations'])->toBeInstanceOf(stdClass::class);
        expect($json[1])->not->toHaveKey('metadata');
    });

    test('serializes empty collection to empty array', function (): void {
        $collection = new TypeDefinitions();

        expect($collection->jsonSerialize())->toBe([]);
    });

    test('handles type definitions with relations', function (): void {
        $relations = new TypeDefinitionRelations([]);
        $viewerRelation = new OpenFGA\Models\Userset(
            computedUserset: new OpenFGA\Models\ObjectRelation(relation: 'viewer'),
        );
        $relations->add('viewer', $viewerRelation);

        $documentType = new TypeDefinition(
            type: 'document',
            relations: $relations,
        );

        $collection = new TypeDefinitions([$documentType]);

        expect($collection->count())->toBe(1);
        expect($collection->get(0)->getType())->toBe('document');
        expect($collection->get(0)->getRelations())->toBe($relations);
    });

    test('returns collection schema', function (): void {
        $schema = TypeDefinitions::schema();

        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(TypeDefinitions::class);
        expect($schema->getItemType())->toBe(TypeDefinition::class);
    });

    test('preserves type definition order', function (): void {
        $types = [];
        $typeNames = ['user', 'group', 'document', 'folder', 'organization'];

        foreach ($typeNames as $typeName) {
            $types[] = new TypeDefinition(type: $typeName);
        }

        $collection = new TypeDefinitions($types);

        for ($i = 0; $i < \count($typeNames); ++$i) {
            expect($collection->get($i)->getType())->toBe($typeNames[$i]);
        }
    });

    test('converts to array', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(type: 'document');
        $collection = new TypeDefinitions([$userType, $documentType]);

        $array = $collection->toArray();

        expect($array)->toBe([$userType, $documentType]);
        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
    });

    test('throws exception for invalid item type', function (): void {
        expect(function (): void {
            $collection = new TypeDefinitions([]);
            $collection->add(new stdClass());
        })->toThrow(TypeError::class);
    });

    test('uses first() method', function (): void {
        $userType = new TypeDefinition(type: 'user');
        $documentType = new TypeDefinition(type: 'document');
        $folderType = new TypeDefinition(type: 'folder');

        $collection = new TypeDefinitions([$userType, $documentType, $folderType]);

        expect($collection->first())->toBe($userType);
    });

    test('first() returns null on empty collection', function (): void {
        $collection = new TypeDefinitions();

        expect($collection->first())->toBeNull();
    });

    test('handles complex authorization model scenario', function (): void {
        // Create a realistic authorization model
        $userType = new TypeDefinition(type: 'user');

        // Group type with member relation
        $groupRelations = new TypeDefinitionRelations([]);
        $memberRelation = new OpenFGA\Models\Userset(
            direct: new stdClass(),
        );
        $groupRelations->add('member', $memberRelation);
        $groupType = new TypeDefinition(type: 'group', relations: $groupRelations);

        // Document type with viewer, editor relations
        $documentRelations = new TypeDefinitionRelations([]);
        $viewerRelation = new OpenFGA\Models\Userset(
            computedUserset: new OpenFGA\Models\ObjectRelation(relation: 'viewer'),
        );
        $editorRelation = new OpenFGA\Models\Userset(
            computedUserset: new OpenFGA\Models\ObjectRelation(relation: 'editor'),
        );
        $documentRelations->add('viewer', $viewerRelation);
        $documentRelations->add('editor', $editorRelation);
        $documentType = new TypeDefinition(type: 'document', relations: $documentRelations);

        $collection = new TypeDefinitions([$userType, $groupType, $documentType]);

        expect($collection->count())->toBe(3);
        expect($collection->get(0)->getType())->toBe('user');
        expect($collection->get(1)->getType())->toBe('group');
        expect($collection->get(1)->getRelations()->count())->toBe(1);
        expect($collection->get(2)->getType())->toBe('document');
        expect($collection->get(2)->getRelations()->count())->toBe(2);
    });
});
