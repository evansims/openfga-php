<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Exceptions\{ClientException, SerializationException};
use OpenFGA\Messages;
use OpenFGA\Models\TupleKey;
use OpenFGA\Schema\CollectionSchemaInterface;
use OpenFGA\Tests\Support\Collections\{InvalidIndexedCollection, InvalidTypeIndexedCollection, TestIndexedCollection};
use stdClass;

describe('IndexedCollection', function (): void {
    test('throws TypeError when $itemType is not defined', function (): void {
        new InvalidIndexedCollection;
    })->throws(ClientException::class, trans(Messages::COLLECTION_UNDEFINED_ITEM_TYPE, ['class' => 'OpenFGA\Tests\Support\Collections\InvalidIndexedCollection']));

    test('throws TypeError when $itemType does not implement ModelInterface', function (): void {
        new InvalidTypeIndexedCollection;
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE, ['interface' => 'OpenFGA\Models\ModelInterface', 'given' => 'stdClass']));

    test('constructs with valid $itemType', function (): void {
        $collection = new TestIndexedCollection;

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
    });

    test('every() returns true for empty collection', function (): void {
        $collection = new TestIndexedCollection;

        $result = $collection->every(fn () => false);

        expect($result)->toBe(true);
    });

    test('every() returns true when all items match callback', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'viewer', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $result = $collection->every(fn (TupleKey $tuple) => 'viewer' === $tuple->getRelation());

        expect($result)->toBe(true);
    });

    test('every() returns false when any item does not match callback', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $result = $collection->every(fn (TupleKey $tuple) => 'viewer' === $tuple->getRelation());

        expect($result)->toBe(false);
    });

    test('filter() returns empty collection when no items match', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $filtered = $collection->filter(fn (TupleKey $tuple) => 'admin' === $tuple->getRelation());

        expect($filtered)->toBeInstanceOf(TestIndexedCollection::class);
        expect($filtered->count())->toBe(0);
        expect($filtered->isEmpty())->toBe(true);
    });

    test('filter() returns matching items', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tuple3 = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'document:3');

        $collection = new TestIndexedCollection($tuple1, $tuple2, $tuple3);

        $filtered = $collection->filter(fn (TupleKey $tuple) => 'viewer' === $tuple->getRelation());

        expect($filtered->count())->toBe(2);
        expect($filtered->toArray())->toBe([$tuple1, $tuple3]);
    });

    test('first() with callback returns first matching item', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tuple3 = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'document:3');

        $collection = new TestIndexedCollection($tuple1, $tuple2, $tuple3);

        $result = $collection->first(fn (TupleKey $tuple) => 'viewer' === $tuple->getRelation());

        expect($result)->toBe($tuple1);
    });

    test('first() with callback returns null when no items match', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $result = $collection->first(fn (TupleKey $tuple) => 'admin' === $tuple->getRelation());

        expect($result)->toBeNull();
    });

    test('key() throws OutOfBoundsException when position is invalid', function (): void {
        $collection = new TestIndexedCollection;

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_POSITION));

    test('offsetSet() throws InvalidArgumentException with wrong type', function (): void {
        $collection = new TestIndexedCollection;
        $wrongType = new stdClass;

        $collection->offsetSet(0, $wrongType);
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_ITEM_INSTANCE, ['expected' => 'OpenFGA\Models\TupleKey', 'given' => 'stdClass']));

    test('offsetSet() with null offset appends item', function (): void {
        $collection = new TestIndexedCollection;
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $collection->offsetSet(null, $tuple);

        expect($collection->count())->toBe(1);
        expect($collection->get(0))->toBe($tuple);
    });

    test('offsetSet() with specific offset sets item at position', function (): void {
        $collection = new TestIndexedCollection;
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $collection->offsetSet(5, $tuple);

        expect($collection->count())->toBe(1);
        expect($collection->offsetGet(5))->toBe($tuple);
        expect($collection->offsetExists(5))->toBe(true);
    });

    test('offsetUnset() removes item and reorders numeric indexes', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tuple3 = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'document:3');

        $collection = new TestIndexedCollection($tuple1, $tuple2, $tuple3);

        expect($collection->count())->toBe(3);
        expect($collection->get(1))->toBe($tuple2);

        $collection->offsetUnset(1);

        expect($collection->count())->toBe(2);
        expect($collection->get(0))->toBe($tuple1);
        expect($collection->get(1))->toBe($tuple3);
        expect($collection->offsetExists(2))->toBe(false);
    });

    test('offsetUnset() with non-numeric key does not reorder', function (): void {
        $collection = new TestIndexedCollection;
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $collection->offsetSet('key', $tuple);
        expect($collection->offsetExists('key'))->toBe(true);

        $collection->offsetUnset('key');
        expect($collection->offsetExists('key'))->toBe(false);
    });

    test('reduce() applies callback to accumulate value', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tuple3 = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'document:3');

        $collection = new TestIndexedCollection($tuple1, $tuple2, $tuple3);

        $result = $collection->reduce([], fn (array $acc, TupleKey $tuple) => [
            ...$acc,
            $tuple->getUser(),
        ]);

        expect($result)->toBe(['user:anne', 'user:bob', 'user:charlie']);
    });

    test('reduce() returns initial value for empty collection', function (): void {
        $collection = new TestIndexedCollection;

        $result = $collection->reduce('initial', fn (string $acc, TupleKey $tuple) => $acc . $tuple->getUser());

        expect($result)->toBe('initial');
    });

    test('some() returns false for empty collection', function (): void {
        $collection = new TestIndexedCollection;

        $result = $collection->some(fn () => true);

        expect($result)->toBe(false);
    });

    test('some() returns true when any item matches callback', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $result = $collection->some(fn (TupleKey $tuple) => 'editor' === $tuple->getRelation());

        expect($result)->toBe(true);
    });

    test('some() returns false when no items match callback', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $result = $collection->some(fn (TupleKey $tuple) => 'admin' === $tuple->getRelation());

        expect($result)->toBe(false);
    });

    test('withItems() creates new collection with additional items', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1);
        $newCollection = $collection->withItems($tuple2);

        expect($collection->count())->toBe(1); // Original unchanged
        expect($newCollection->count())->toBe(2); // New collection has both
        expect($newCollection)->not->toBe($collection); // Different instance
        expect($newCollection->toArray())->toBe([$tuple1, $tuple2]);
    });

    test('schema() throws exception when $itemType is not defined', function (): void {
        InvalidIndexedCollection::schema();
    })->throws(SerializationException::class);

    test('schema() throws exception when $itemType is invalid', function (): void {
        InvalidTypeIndexedCollection::schema();
    })->throws(SerializationException::class);

    test('schema() returns valid schema for concrete collection', function (): void {
        $schema = TestIndexedCollection::schema();

        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('schema() caches schema instances', function (): void {
        $schema1 = TestIndexedCollection::schema();
        $schema2 = TestIndexedCollection::schema();

        expect($schema1)->toBe($schema2); // Same instance
    });

    test('normalizeItems() handles mixed iterable and single items', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');
        $tuple3 = new TupleKey(user: 'user:charlie', relation: 'viewer', object: 'document:3');

        // Mix single items and arrays
        $collection = new TestIndexedCollection($tuple1, [$tuple2, $tuple3]);

        expect($collection->count())->toBe(3);
        expect($collection->toArray())->toBe([$tuple1, $tuple2, $tuple3]);
    });

    test('iterator position management works correctly', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        // Test iterator methods
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple1);
        expect($collection->key())->toBe(0);

        $collection->next();
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple2);
        expect($collection->key())->toBe(1);

        $collection->next();
        expect($collection->valid())->toBe(false);

        $collection->rewind();
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple1);
        expect($collection->key())->toBe(0);
    });

    test('collection works with foreach iteration', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestIndexedCollection($tuple1, $tuple2);

        $iterated = [];
        foreach ($collection as $key => $value) {
            $iterated[$key] = $value;
        }

        expect($iterated)->toBe([0 => $tuple1, 1 => $tuple2]);
    });
});
