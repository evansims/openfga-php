<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use Exception;
use OpenFGA\Models\{BatchCheckItem, BatchCheckItemInterface};
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Schema\CollectionSchemaInterface;
use TypeError;

use function count;
use function OpenFGA\{tuple, tuples};

it('creates an empty collection', function (): void {
    $collection = new BatchCheckItems;

    expect($collection->count())->toBe(0);
    expect($collection->isEmpty())->toBeTrue();
    expect($collection->toArray())->toBeEmpty();
});

it('creates a collection with items in constructor', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);

    expect($collection->count())->toBe(2);
    expect($collection->isEmpty())->toBeFalse();
    expect($collection->get(0))->toBe($item1);
    expect($collection->get(1))->toBe($item2);
});

it('adds items to collection', function (): void {
    $collection = new BatchCheckItems;
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection->add($item1);
    expect($collection->count())->toBe(1);

    $collection->add($item2);
    expect($collection->count())->toBe(2);

    expect($collection->get(0))->toBe($item1);
    expect($collection->get(1))->toBe($item2);
});

it('supports array access', function (): void {
    $collection = new BatchCheckItems;
    $item = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');

    $collection[0] = $item;
    expect($collection[0])->toBe($item);
    expect(isset($collection[0]))->toBeTrue();
    expect(isset($collection[1]))->toBeFalse();

    unset($collection[0]);
    expect(isset($collection[0]))->toBeFalse();
    expect($collection->count())->toBe(0);
});

it('throws exception when adding invalid item type', function (): void {
    $collection = new BatchCheckItems;

    expect(fn () => $collection->add('not-a-batch-check-item'))
        ->toThrow(TypeError::class);
});

it('filters items correctly', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'alice-reader');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'bob-writer');
    $item3 = new BatchCheckItem(tuple('user:charlie', 'reader', 'document:plan'), 'charlie-reader');

    $collection = new BatchCheckItems([$item1, $item2, $item3]);

    // Filter for reader relationships
    $readers = $collection->filter(
        fn (BatchCheckItemInterface $item): bool => 'reader' === $item->getTupleKey()->getRelation(),
    );

    expect($readers->count())->toBe(2);
    expect($readers->get(0)->getCorrelationId())->toBe('alice-reader');
    expect($readers->get(1)->getCorrelationId())->toBe('charlie-reader');
});

it('finds first item correctly', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'alice-reader');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'bob-writer');

    $collection = new BatchCheckItems([$item1, $item2]);

    $first = $collection->first();
    expect($first)->toBe($item1);

    $emptyCollection = new BatchCheckItems;
    expect($emptyCollection->first())->toBeNull();
});

it('checks if some items match condition', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'alice-reader');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'bob-writer');

    $collection = new BatchCheckItems([$item1, $item2]);

    expect($collection->some(
        fn (BatchCheckItemInterface $item): bool => 'writer' === $item->getTupleKey()->getRelation(),
    ))->toBeTrue();

    expect($collection->some(
        fn (BatchCheckItemInterface $item): bool => 'admin' === $item->getTupleKey()->getRelation(),
    ))->toBeFalse();
});

it('checks if every item matches condition', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'alice-reader');
    $item2 = new BatchCheckItem(tuple('user:bob', 'reader', 'document:spec'), 'bob-reader');

    $collection = new BatchCheckItems([$item1, $item2]);

    expect($collection->every(
        fn (BatchCheckItemInterface $item): bool => 'reader' === $item->getTupleKey()->getRelation(),
    ))->toBeTrue();

    expect($collection->every(
        fn (BatchCheckItemInterface $item): bool => 'document:budget' === $item->getTupleKey()->getObject(),
    ))->toBeFalse();
});

it('reduces items to a single value', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'alice-reader');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'bob-writer');
    $item3 = new BatchCheckItem(tuple('user:charlie', 'admin', 'document:plan'), 'charlie-admin');

    $collection = new BatchCheckItems([$item1, $item2, $item3]);

    // Count unique relations
    $relationCounts = $collection->reduce(
        [],
        function (array $acc, BatchCheckItemInterface $item): array {
            $relation = $item->getTupleKey()->getRelation();
            $acc[$relation] = ($acc[$relation] ?? 0) + 1;

            return $acc;
        },
    );

    expect($relationCounts)->toBe([
        'reader' => 1,
        'writer' => 1,
        'admin' => 1,
    ]);
});

it('is iterable', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);

    $iterations = 0;

    foreach ($collection as $index => $item) {
        expect($index)->toBe($iterations);
        expect($item)->toBeInstanceOf(BatchCheckItemInterface::class);
        $iterations++;
    }

    expect($iterations)->toBe(2);
});

it('is countable', function (): void {
    $collection = new BatchCheckItems;
    expect(count($collection))->toBe(0);

    $item = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $collection->add($item);
    expect(count($collection))->toBe(1);
});

it('clears all items', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);
    expect($collection->count())->toBe(2);

    $collection->clear();
    expect($collection->count())->toBe(0);
    expect($collection->isEmpty())->toBeTrue();
});

it('creates new collection with items', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems;
    $newCollection = $collection->withItems([$item1, $item2]);

    expect($collection->count())->toBe(0); // Original unchanged
    expect($newCollection->count())->toBe(2);
    expect($newCollection->get(0))->toBe($item1);
    expect($newCollection->get(1))->toBe($item2);
});

it('serializes to JSON correctly', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);
    $json = $collection->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveCount(2);
    expect($json[0])->toBeArray();
    expect($json[1])->toBeArray();
    expect($json[0]['correlation_id'])->toBe('id-1');
    expect($json[1]['correlation_id'])->toBe('id-2');
});

it('converts to array correctly', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);
    $array = $collection->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveCount(2);
    expect($array[0])->toBeInstanceOf(BatchCheckItem::class);
    expect($array[1])->toBeInstanceOf(BatchCheckItem::class);
    expect($array[0]->getCorrelationId())->toBe('id-1');
    expect($array[1]->getCorrelationId())->toBe('id-2');
});

it('has valid schema', function (): void {
    $schema = BatchCheckItems::schema();

    expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
    expect($schema->getClassName())->toBe(BatchCheckItems::class);
    expect($schema->getItemType())->toBe(BatchCheckItem::class);
    expect($schema->requiresItems())->toBeFalse();
});

it('gets item by index', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    $collection = new BatchCheckItems([$item1, $item2]);

    expect($collection->get(0))->toBe($item1);
    expect($collection->get(1))->toBe($item2);
    expect($collection->get(2))->toBeNull(); // Out of bounds
});

it('works with direct constructor', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'id-1');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'id-2');

    // Test direct constructor
    $collection = new BatchCheckItems([$item1, $item2]);

    expect($collection)->toBeInstanceOf(BatchCheckItems::class);
    expect($collection->count())->toBe(2);
    expect($collection->get(0))->toBe($item1);
    expect($collection->get(1))->toBe($item2);
});

it('validates correlation ID uniqueness within collection', function (): void {
    $item1 = new BatchCheckItem(tuple('user:alice', 'reader', 'document:budget'), 'duplicate-id');
    $item2 = new BatchCheckItem(tuple('user:bob', 'writer', 'document:spec'), 'duplicate-id');

    $collection = new BatchCheckItems;
    $collection->add($item1);

    // This should be allowed at the collection level - uniqueness is enforced at API level
    expect(fn () => $collection->add($item2))->not->toThrow(Exception::class);
    expect($collection->count())->toBe(2);
});

it('handles complex contextual tuples scenario', function (): void {
    $contextualTuples = tuples(
        tuple('user:manager', 'approver', 'document:budget'),
        tuple('user:finance', 'reviewer', 'document:budget'),
    );

    $item = new BatchCheckItem(
        tupleKey: tuple('user:alice', 'reader', 'document:budget'),
        correlationId: 'complex-scenario-1',
        contextualTuples: $contextualTuples,
        context: (object) ['department' => 'engineering', 'urgent' => true],
    );

    $collection = new BatchCheckItems([$item]);

    expect($collection->count())->toBe(1);
    expect($collection->get(0)->getContextualTuples()->count())->toBe(2);
    expect($collection->get(0)->getContext()->department)->toBe('engineering');
});
