<?php

declare(strict_types=1);

use OpenFGA\Models\{RelationReference, RelationReferences};

test('empty collection', function (): void {
    $collection = new RelationReferences();

    expect($collection)->toHaveCount(0);
});

test('add relation reference', function (): void {
    $collection = new RelationReferences();
    $reference = new RelationReference('document');

    $collection->add('reader', $reference);

    expect($collection)->toHaveCount(1)
        ->and($collection['reader'])->toBe($reference);
});

test('add multiple relation references via constructor', function (): void {
    $reference1 = new RelationReference('document');
    $reference2 = new RelationReference('folder');

    $collection = new RelationReferences([
        'reader' => $reference1,
        'writer' => $reference2,
    ]);

    expect($collection)->toHaveCount(2)
        ->and($collection['reader'])->toBe($reference1)
        ->and($collection['writer'])->toBe($reference2);
});

test('add with empty key throws exception', function (): void {
    $collection = new RelationReferences();
    $reference = new RelationReference('document');

    expect(fn () => $collection->add('', $reference))
        ->toThrow(InvalidArgumentException::class, 'Key cannot be empty');
});

test('add with duplicate key throws exception', function (): void {
    $collection = new RelationReferences();
    $reference1 = new RelationReference('document');
    $reference2 = new RelationReference('folder');

    $collection->add('reader', $reference1);

    expect(fn () => $collection->add('reader', $reference2))
        ->toThrow(InvalidArgumentException::class, 'Key "reader" already exists in the collection');
});

test('add with invalid key characters throws exception', function (): void {
    $collection = new RelationReferences();
    $reference = new RelationReference('document');

    expect(fn () => $collection->add('invalid@key', $reference))
        ->toThrow(InvalidArgumentException::class, 'Key can only contain alphanumeric characters, underscores, and hyphens');
});

test('json serialize', function (): void {
    $reference1 = new RelationReference('document', 'reader');
    $reference2 = new RelationReference('folder', 'writer');

    $collection = new RelationReferences([
        'doc_reader' => $reference1,
        'folder_writer' => $reference2,
    ]);

    $result = $collection->jsonSerialize();

    expect($result)->toBe([
        'doc_reader' => $reference1->jsonSerialize(),
        'folder_writer' => $reference2->jsonSerialize(),
    ]);
});

test('iteration', function (): void {
    $reference1 = new RelationReference('document', 'reader');
    $reference2 = new RelationReference('folder', 'writer');

    $collection = new RelationReferences([
        'reader' => $reference1,
        'writer' => $reference2,
    ]);

    $items = [];
    foreach ($collection as $key => $reference) {
        $items[$key] = $reference;
    }

    expect($items)->toBe([
        'reader' => $reference1,
        'writer' => $reference2,
    ]);
});

test('schema', function (): void {
    $schema = RelationReferences::schema();

    expect($schema->getClassName())->toBe(RelationReferences::class)
        ->and($schema->getItemType())->toBe('OpenFGA\\Models\\RelationReference');
});
