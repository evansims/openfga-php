<?php

declare(strict_types=1);

use OpenFGA\Responses\{ListObjectsResponse, ListObjectsResponseInterface};

test('ListObjectsResponse implements ListObjectsResponseInterface', function (): void {
    $response = new ListObjectsResponse([]);
    expect($response)->toBeInstanceOf(ListObjectsResponseInterface::class);
});

test('ListObjectsResponse constructs with empty objects array', function (): void {
    $response = new ListObjectsResponse([]);

    expect($response->getObjects())->toBe([]);
    expect($response->getObjects())->toHaveCount(0);
});

test('ListObjectsResponse constructs with single object', function (): void {
    $objects = ['document:budget.pdf'];
    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects())->toHaveCount(1);
    expect($response->getObjects()[0])->toBe('document:budget.pdf');
});

test('ListObjectsResponse constructs with multiple objects', function (): void {
    $objects = [
        'document:budget.pdf',
        'document:report.docx',
        'folder:reports',
        'system:admin-panel',
        'user:alice',
    ];

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects())->toHaveCount(5);
});

test('ListObjectsResponse preserves object order', function (): void {
    $objects = [
        'document:z-last.pdf',
        'document:a-first.pdf',
        'document:m-middle.pdf',
    ];

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects()[0])->toBe('document:z-last.pdf');
    expect($response->getObjects()[1])->toBe('document:a-first.pdf');
    expect($response->getObjects()[2])->toBe('document:m-middle.pdf');
});

test('ListObjectsResponse handles objects with special characters', function (): void {
    $objects = [
        'document:file with spaces.pdf',
        'document:file-with-dashes.pdf',
        'document:file_with_underscores.pdf',
        'document:file.with.dots.pdf',
        'document:file@with@symbols.pdf',
    ];

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects())->toHaveCount(5);
});

test('ListObjectsResponse handles large object list', function (): void {
    $objects = [];
    for ($i = 1; $i <= 1000; ++$i) {
        $objects[] = "document:file{$i}.pdf";
    }

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toHaveCount(1000);
    expect($response->getObjects()[0])->toBe('document:file1.pdf');
    expect($response->getObjects()[999])->toBe('document:file1000.pdf');
});

test('ListObjectsResponse handles duplicate objects', function (): void {
    $objects = [
        'document:duplicate.pdf',
        'document:unique.pdf',
        'document:duplicate.pdf',
        'document:another.pdf',
        'document:duplicate.pdf',
    ];

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects())->toHaveCount(5);

    // Count occurrences of duplicate
    $duplicateCount = array_count_values($response->getObjects())['document:duplicate.pdf'];
    expect($duplicateCount)->toBe(3);
});

// Note: fromResponse method testing would require integration tests due to SchemaValidator being final
// These tests focus on the model's direct functionality

test('ListObjectsResponse schema returns expected structure', function (): void {
    $schema = ListObjectsResponse::schema();

    expect($schema)->toBeInstanceOf(OpenFGA\Schema\SchemaInterface::class);
    expect($schema->getClassName())->toBe(ListObjectsResponse::class);

    $properties = $schema->getProperties();
    expect($properties)->toHaveCount(1);

    expect($properties)->toHaveKeys(['objects']);

    expect($properties['objects']->name)->toBe('objects');
    expect($properties['objects']->type)->toBe('array');
    expect($properties['objects']->required)->toBeTrue();
    expect($properties['objects']->items)->toBe(['type' => 'string']);
});

test('ListObjectsResponse schema is cached', function (): void {
    $schema1 = ListObjectsResponse::schema();
    $schema2 = ListObjectsResponse::schema();

    expect($schema1)->toBe($schema2);
});

test('ListObjectsResponse handles empty string objects', function (): void {
    $objects = ['', 'document:valid.pdf', ''];
    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects()[0])->toBe('');
    expect($response->getObjects()[2])->toBe('');
});

test('ListObjectsResponse handles various object types', function (): void {
    $objects = [
        'document:file.pdf',
        'folder:documents',
        'user:alice',
        'group:engineering',
        'system:admin',
        'namespace:default',
        'role:editor',
    ];

    $response = new ListObjectsResponse($objects);

    expect($response->getObjects())->toBe($objects);
    expect($response->getObjects())->toHaveCount(7);
});
