<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{RelationMetadata, RelationReference, RelationReferences, SourceInfo};

test('constructor with all properties', function (): void {
    $module = 'test-module';
    $directlyRelatedUserTypes = new RelationReferences([
        new RelationReference('group', 'member'),
        new RelationReference('user', null),
    ]);
    $sourceInfo = new SourceInfo('test-file');

    $relationMetadata = new RelationMetadata(
        module: $module,
        directlyRelatedUserTypes: $directlyRelatedUserTypes,
        sourceInfo: $sourceInfo,
    );

    expect($relationMetadata->getModule())->toBe($module)
        ->and($relationMetadata->getDirectlyRelatedUserTypes())->toBe($directlyRelatedUserTypes)
        ->and($relationMetadata->getSourceInfo())->toBe($sourceInfo);
});

test('constructor with minimal properties', function (): void {
    $relationMetadata = new RelationMetadata();

    expect($relationMetadata->getModule())->toBeNull()
        ->and($relationMetadata->getDirectlyRelatedUserTypes())->toBeNull()
        ->and($relationMetadata->getSourceInfo())->toBeNull();
});

test('json serialize with all properties', function (): void {
    $module = 'test-module';
    $directlyRelatedUserTypes = new RelationReferences([
        new RelationReference('group', 'member'),
        new RelationReference('user', null),
    ]);
    $sourceInfo = new SourceInfo('test-file');

    $relationMetadata = new RelationMetadata(
        module: $module,
        directlyRelatedUserTypes: $directlyRelatedUserTypes,
        sourceInfo: $sourceInfo,
    );

    $result = $relationMetadata->jsonSerialize();

    expect($result)->toMatchArray([
        'module' => $module,
        'directly_related_user_types' => $directlyRelatedUserTypes->jsonSerialize(),
        'source_info' => $sourceInfo->jsonSerialize(),
    ]);
});

test('json serialize with null properties', function (): void {
    $relationMetadata = new RelationMetadata();

    $result = $relationMetadata->jsonSerialize();

    expect($result)->toBeArray()
        ->toHaveCount(0);
});

test('schema', function (): void {
    $schema = RelationMetadata::schema();

    expect($schema->getClassName())->toBe(RelationMetadata::class);

    $properties = $schema->getProperties();
    expect($properties)->toBeArray()
        ->toHaveCount(3); // Assuming there are 3 properties

    // Check module property
    expect($properties['module']->name)->toBe('module')
        ->and($properties['module']->type)->toBe('string')
        ->and($properties['module']->required)->toBeFalse();

    // Check directly_related_user_types property
    expect($properties['directly_related_user_types']->name)->toBe('directly_related_user_types')
        ->and($properties['directly_related_user_types']->type)->toBe('OpenFGA\\Models\\RelationReferences')
        ->and($properties['directly_related_user_types']->required)->toBeFalse();

    // Check source_info property
    expect($properties['source_info']->name)->toBe('source_info')
        ->and($properties['source_info']->type)->toBe('OpenFGA\\Models\\SourceInfo')
        ->and($properties['source_info']->required)->toBeFalse();
});
