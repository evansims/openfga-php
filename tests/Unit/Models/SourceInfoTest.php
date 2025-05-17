<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\SourceInfo;

test('constructor and getter', function (): void {
    $file = 'path/to/source.txt';
    $sourceInfo = new SourceInfo($file);

    expect($sourceInfo->getFile())->toBe($file);
});

test('constructor with empty file throws exception', function (): void {
    expect(fn () => new SourceInfo(''))
        ->toThrow(InvalidArgumentException::class, 'SourceInfo::$file cannot be empty.');

    $file = 'path/to/source.txt';
    $sourceInfo = new SourceInfo($file);

    expect($sourceInfo->getFile())->toBe($file);
});

test('json serialize', function (): void {
    $file = 'path/to/source.txt';
    $sourceInfo = new SourceInfo($file);

    $result = $sourceInfo->jsonSerialize();

    expect($result)->toBe([
        'file' => $file,
    ]);
});

test('schema', function (): void {
    $schema = SourceInfo::schema();

    expect($schema->getClassName())->toBe(SourceInfo::class)
        ->and($schema->getProperties())->toHaveCount(1)
        ->and($schema->getProperty('file')->name)->toBe('file')
        ->and($schema->getProperty('file')->type)->toBe('string')
        ->and($schema->getProperty('file')->required)->toBeTrue();
});
