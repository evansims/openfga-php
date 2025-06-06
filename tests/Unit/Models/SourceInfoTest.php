<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Messages;
use OpenFGA\Models\{SourceInfo, SourceInfoInterface};
use OpenFGA\Schemas\SchemaInterface;

describe('SourceInfo Model', function (): void {
    test('implements SourceInfoInterface', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');

        expect($sourceInfo)->toBeInstanceOf(SourceInfoInterface::class);
    });

    test('constructs with file parameter', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');

        expect($sourceInfo->getFile())->toBe('model.fga');
    });

    test('throws exception for empty file string', function (): void {
        new SourceInfo(file: '');
    })->throws(ClientException::class, trans(Messages::MODEL_SOURCE_INFO_FILE_EMPTY));

    test('handles various file paths', function (): void {
        $filePaths = [
            'simple.fga',
            'path/to/model.fga',
            '/absolute/path/to/model.fga',
            '../relative/path/model.fga',
            'file-with-dashes.fga',
            'file_with_underscores.fga',
            'file.with.dots.fga',
        ];

        foreach ($filePaths as $path) {
            $sourceInfo = new SourceInfo(file: $path);
            expect($sourceInfo->getFile())->toBe($path);
        }
    });

    test('serializes to JSON', function (): void {
        $sourceInfo = new SourceInfo(file: 'model.fga');

        $json = $sourceInfo->jsonSerialize();

        expect($json)->toBe(['file' => 'model.fga']);
    });

    test('returns schema instance', function (): void {
        $schema = SourceInfo::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(SourceInfo::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(1);

        $fileProp = $properties[array_keys($properties)[0]];
        expect($fileProp->name)->toBe('file');
        expect($fileProp->type)->toBe('string');
        expect($fileProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = SourceInfo::schema();
        $schema2 = SourceInfo::schema();

        expect($schema1)->toBe($schema2);
    });

    test('preserves whitespace in file paths', function (): void {
        $sourceInfo = new SourceInfo(file: '  model.fga  ');

        expect($sourceInfo->getFile())->toBe('  model.fga  ');
    });

    test('handles unicode characters in file paths', function (): void {
        $sourceInfo = new SourceInfo(file: 'модель.fga');

        expect($sourceInfo->getFile())->toBe('модель.fga');
    });
});
