<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{DifferenceV1, Userset};

beforeEach(function (): void {
    $this->base = new Userset(direct: (object) ['type' => 'user', 'id' => 'user1']);
    $this->subtract = new Userset(direct: (object) ['type' => 'user', 'id' => 'user2']);
});

test('constructor and getters', function (): void {
    $difference = new DifferenceV1($this->base, $this->subtract);

    expect($difference->getBase())->toBe($this->base)
        ->and($difference->getSubtract())->toBe($this->subtract);
});

test('json serialize', function (): void {
    $difference = new DifferenceV1($this->base, $this->subtract);
    $result = $difference->jsonSerialize();

    expect($result)->toBe([
        'base' => $this->base->jsonSerialize(),
        'subtract' => $this->subtract->jsonSerialize(),
    ]);
});

test('schema', function (): void {
    $schema = DifferenceV1::schema();

    expect($schema->getClassName())->toBe(DifferenceV1::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray()
        ->and($properties)->toHaveCount(2);

    // Check base property
    expect($properties)->toHaveKey('base')
        ->and($properties['base']->name)->toBe('base')
        ->and($properties['base']->type)->toBe('OpenFGA\\Models\\Userset')
        ->and($properties['base']->required)->toBeTrue();

    // Check subtract property
    expect($properties)->toHaveKey('subtract')
        ->and($properties['subtract']->name)->toBe('subtract')
        ->and($properties['subtract']->type)->toBe('OpenFGA\\Models\\Userset')
        ->and($properties['subtract']->required)->toBeTrue();
});
