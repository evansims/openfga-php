<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Metadata, RelationMetadata, SourceInfo};

beforeEach(function (): void {
    $this->module = 'test-module';
    $this->relations = new RelationMetadata();
    $this->sourceInfo = new SourceInfo('test-file.txt');
});

test('constructor with all properties', function (): void {
    $metadata = new Metadata(
        module: $this->module,
        relations: $this->relations,
        sourceInfo: $this->sourceInfo,
    );

    expect($metadata->getModule())->toBe($this->module)
        ->and($metadata->getRelations())->toBe($this->relations)
        ->and($metadata->getSourceInfo())->toBe($this->sourceInfo);
});

test('constructor with minimal properties', function (): void {
    $metadata = new Metadata();

    expect($metadata->getModule())->toBeNull()
        ->and($metadata->getRelations())->toBeNull()
        ->and($metadata->getSourceInfo())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $metadata = new Metadata(
        module: $this->module,
        relations: $this->relations,
        sourceInfo: $this->sourceInfo,
    );

    $result = $metadata->jsonSerialize();

    expect($result)->toBe([
        'module' => $this->module,
        'relations' => $this->relations->jsonSerialize(),
        'source_info' => $this->sourceInfo->jsonSerialize(),
    ]);
});

test('json serialize with null properties', function (): void {
    $metadata = new Metadata();

    $result = $metadata->jsonSerialize();

    expect($result)->toBe([]);
});

test('schema', function (): void {
    $schema = Metadata::schema();

    expect($schema->getClassName())->toBe(Metadata::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray()
        ->toHaveCount(3);

    // Check module property
    expect($properties)->toHaveKey('module')
        ->and($properties['module']->name)->toBe('module')
        ->and($properties['module']->type)->toBe('string')
        ->and($properties['module']->required)->toBeFalse();

    // Check relations property
    expect($properties)->toHaveKey('relations')
        ->and($properties['relations']->name)->toBe('relations')
        ->and($properties['relations']->type)->toBe('OpenFGA\\Models\\RelationMetadata')
        ->and($properties['relations']->required)->toBeFalse();

    // Check source_info property
    expect($properties)->toHaveKey('source_info')
        ->and($properties['source_info']->name)->toBe('source_info')
        ->and($properties['source_info']->type)->toBe('OpenFGA\\Models\\SourceInfo')
        ->and($properties['source_info']->required)->toBeFalse();
});
