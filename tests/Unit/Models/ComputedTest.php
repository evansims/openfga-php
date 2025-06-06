<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Computed, ComputedInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('Computed Model', function (): void {
    test('implements ComputedInterface', function (): void {
        $computed = new Computed(userset: 'viewer');

        expect($computed)->toBeInstanceOf(ComputedInterface::class);
    });

    test('constructs with userset string', function (): void {
        $computed = new Computed(userset: 'viewer');

        expect($computed->getUserset())->toBe('viewer');
    });

    test('constructs with complex userset string', function (): void {
        $computed = new Computed(userset: 'owner#member');

        expect($computed->getUserset())->toBe('owner#member');
    });

    test('serializes to JSON', function (): void {
        $computed = new Computed(userset: 'viewer');

        expect($computed->jsonSerialize())->toBe([
            'userset' => 'viewer',
        ]);
    });

    test('serializes complex userset string', function (): void {
        $computed = new Computed(userset: 'group:engineering#member');

        expect($computed->jsonSerialize())->toBe([
            'userset' => 'group:engineering#member',
        ]);
    });

    test('returns schema instance', function (): void {
        $schema = Computed::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Computed::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $propertyNames = array_keys($properties);
        expect($propertyNames)->toBe(['userset']);
    });

    test('schema properties have correct configuration', function (): void {
        $schema = Computed::schema();
        $properties = $schema->getProperties();

        // Userset property
        $usersetProp = $properties['userset'];
        expect($usersetProp->name)->toBe('userset');
        expect($usersetProp->type)->toBe('string');
        expect($usersetProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = Computed::schema();
        $schema2 = Computed::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles typical authorization patterns', function (): void {
        // Pattern 1: Simple relation
        $viewerComputed = new Computed(userset: 'viewer');
        expect($viewerComputed->jsonSerialize())->toBe(['userset' => 'viewer']);

        // Pattern 2: Userset with type
        $userComputed = new Computed(userset: 'user:*');
        expect($userComputed->jsonSerialize())->toBe(['userset' => 'user:*']);

        // Pattern 3: Userset with relation
        $memberComputed = new Computed(userset: 'group:engineering#member');
        expect($memberComputed->jsonSerialize())->toBe(['userset' => 'group:engineering#member']);

        // Pattern 4: Complex relation path
        $nestedComputed = new Computed(userset: 'parent#viewer');
        expect($nestedComputed->jsonSerialize())->toBe(['userset' => 'parent#viewer']);
    });
});
