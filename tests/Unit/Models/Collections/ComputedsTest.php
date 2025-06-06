<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Models\Collections\{Computeds, ComputedsInterface};
use OpenFGA\Models\Computed;
use OpenFGA\Schemas\{CollectionSchemaInterface, SchemaInterface};

describe('Computeds Collection', function (): void {
    test('implements interface', function (): void {
        $collection = new Computeds;

        expect($collection)->toBeInstanceOf(ComputedsInterface::class);
    });

    test('creates empty', function (): void {
        $collection = new Computeds;

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBeTrue();
    });

    test('creates with array of computed objects', function (): void {
        $computed1 = new Computed(userset: 'viewer');
        $computed2 = new Computed(userset: 'editor');
        $computed3 = new Computed(userset: 'owner');

        $collection = new Computeds([$computed1, $computed2, $computed3]);

        expect($collection->count())->toBe(3);
        expect($collection->isEmpty())->toBeFalse();
    });

    test('adds computed objects', function (): void {
        $collection = new Computeds;

        $computed = new Computed(userset: 'viewer');
        $collection->add($computed);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($computed);
    });

    test('checks if computed exists', function (): void {
        $computed = new Computed(userset: 'viewer');
        $collection = new Computeds([$computed]);

        expect(isset($collection[0]))->toBeTrue();
        expect(isset($collection[1]))->toBeFalse();
    });

    test('iterates over computed objects', function (): void {
        $computed1 = new Computed(userset: 'viewer');
        $computed2 = new Computed(userset: 'editor');
        $computed3 = new Computed(userset: 'owner');

        $collection = new Computeds([$computed1, $computed2, $computed3]);

        $usersets = [];

        foreach ($collection as $computed) {
            $usersets[] = $computed->getUserset();
        }

        expect($usersets)->toBe(['viewer', 'editor', 'owner']);
    });

    test('toArray', function (): void {
        $computed1 = new Computed(userset: 'viewer');
        $computed2 = new Computed(userset: 'editor');

        $collection = new Computeds([$computed1, $computed2]);
        $array = $collection->toArray();

        expect($array)->toBeArray();
        expect($array)->toHaveCount(2);
        expect($array[0])->toBe($computed1);
        expect($array[1])->toBe($computed2);
    });

    test('jsonSerialize', function (): void {
        $computed1 = new Computed(userset: 'viewer');
        $computed2 = new Computed(userset: 'editor');

        $collection = new Computeds([$computed1, $computed2]);
        $json = $collection->jsonSerialize();

        expect($json)->toBeArray();
        expect($json)->toHaveCount(2);
        expect($json[0])->toBe(['userset' => 'viewer']);
        expect($json[1])->toBe(['userset' => 'editor']);
    });

    test('schema', function (): void {
        $schema = Computeds::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema->getClassName())->toBe(Computeds::class);
    });

    test('schema is cached', function (): void {
        $schema1 = Computeds::schema();
        $schema2 = Computeds::schema();

        expect($schema1)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema2)->toBeInstanceOf(CollectionSchemaInterface::class);
        expect($schema1)->toBe($schema2, 'Expected the same schema instance to be returned on subsequent calls');
    });

    test('handles complex userset patterns', function (): void {
        $computeds = [
            new Computed(userset: 'user:*'),
            new Computed(userset: 'group:engineering#member'),
            new Computed(userset: 'parent#viewer'),
            new Computed(userset: 'organization#admin'),
        ];

        $collection = new Computeds($computeds);

        expect($collection->count())->toBe(4);

        $json = $collection->jsonSerialize();
        expect($json)->toBe([
            ['userset' => 'user:*'],
            ['userset' => 'group:engineering#member'],
            ['userset' => 'parent#viewer'],
            ['userset' => 'organization#admin'],
        ]);
    });

    test('filters computed objects by pattern', function (): void {
        $collection = new Computeds([
            new Computed(userset: 'viewer'),
            new Computed(userset: 'editor'),
            new Computed(userset: 'owner'),
            new Computed(userset: 'viewer#admin'),
            new Computed(userset: 'group:viewer#member'),
        ]);

        // Filter usersets containing 'viewer'
        $filtered = [];

        foreach ($collection as $computed) {
            if (str_contains($computed->getUserset(), 'viewer')) {
                $filtered[] = $computed->getUserset();
            }
        }

        expect($filtered)->toBe(['viewer', 'viewer#admin', 'group:viewer#member']);
    });

    test('supports building permission hierarchies', function (): void {
        // Build a typical permission hierarchy
        $viewerComputeds = new Computeds([
            new Computed(userset: 'viewer'),
            new Computed(userset: 'editor'),
            new Computed(userset: 'owner'),
        ]);

        $editorComputeds = new Computeds([
            new Computed(userset: 'editor'),
            new Computed(userset: 'owner'),
        ]);

        $ownerComputeds = new Computeds([
            new Computed(userset: 'owner'),
        ]);

        expect($viewerComputeds->count())->toBe(3);
        expect($editorComputeds->count())->toBe(2);
        expect($ownerComputeds->count())->toBe(1);
    });

    test('handles empty collection edge cases', function (): void {
        $collection = new Computeds;

        expect($collection->isEmpty())->toBeTrue();
        expect($collection->toArray())->toBe([]);
        expect($collection->jsonSerialize())->toBe([]);

        // Test iteration on empty collection
        $count = 0;

        foreach ($collection as $item) {
            ++$count;
        }
        expect($count)->toBe(0);
    });
});
