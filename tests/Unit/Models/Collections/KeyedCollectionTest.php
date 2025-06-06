<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models\Collections;

use OpenFGA\Exceptions\{ClientException, SerializationException};
use OpenFGA\Messages;
use OpenFGA\Models\Collections\KeyedCollection;
use OpenFGA\Models\{TupleKey, User};
use OpenFGA\Schemas\CollectionSchemaInterface;
use OpenFGA\Tests\Support\Collections\{InvalidKeyedCollection, InvalidTypeKeyedCollection, TestKeyedCollection};
use ReflectionClass;
use stdClass;

describe('KeyedCollection', function (): void {
    test('throws TypeError when $itemType is not defined', function (): void {
        new InvalidKeyedCollection([]);
    })->throws(ClientException::class, trans(Messages::COLLECTION_UNDEFINED_ITEM_TYPE, ['class' => 'OpenFGA\Tests\Support\Collections\InvalidKeyedCollection']));

    test('throws TypeError when $itemType does not implement ModelInterface', function (): void {
        new InvalidTypeKeyedCollection([]);
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_ITEM_TYPE_INTERFACE, ['interface' => 'OpenFGA\Models\ModelInterface', 'given' => 'stdClass']));

    test('constructs with valid $itemType and empty array', function (): void {
        $collection = new TestKeyedCollection([]);

        expect($collection->count())->toBe(0);
        expect($collection->isEmpty())->toBe(true);
    });

    test('constructs with associative array', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection([
            'key1' => $tuple1,
            'key2' => $tuple2,
        ]);

        expect($collection->count())->toBe(2);
        expect($collection->has('key1'))->toBe(true);
        expect($collection->has('key2'))->toBe(true);
        expect($collection->get('key1'))->toBe($tuple1);
        expect($collection->get('key2'))->toBe($tuple2);
    });

    test('constructs with numeric array converting indices to string keys', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection([$tuple1, $tuple2]);

        expect($collection->count())->toBe(2);
        expect($collection->has('0'))->toBe(true);
        expect($collection->has('1'))->toBe(true);
        expect($collection->get('0'))->toBe($tuple1);
        expect($collection->get('1'))->toBe($tuple2);
    });

    test('add() throws ClientException with wrong model type', function (): void {
        $collection = new TestKeyedCollection([]);
        // User implements ModelInterface but is not TupleKey
        $wrongType = new User('user:anne');

        $collection->add('key', $wrongType);
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_ITEM_INSTANCE, ['expected' => 'OpenFGA\Models\TupleKey', 'given' => 'OpenFGA\Models\User']));

    test('add() adds item with string key', function (): void {
        $collection = new TestKeyedCollection([]);
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $result = $collection->add('test-key', $tuple);

        expect($result)->toBe($collection); // Fluent interface
        expect($collection->count())->toBe(1);
        expect($collection->has('test-key'))->toBe(true);
        expect($collection->get('test-key'))->toBe($tuple);
    });

    test('get() returns null for non-existent key', function (): void {
        $collection = new TestKeyedCollection([]);

        expect($collection->get('non-existent'))->toBeNull();
    });

    test('has() returns false for non-existent key', function (): void {
        $collection = new TestKeyedCollection([]);

        expect($collection->has('non-existent'))->toBe(false);
    });

    test('key() throws OutOfBoundsException when position is invalid', function (): void {
        $collection = new TestKeyedCollection([]);

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => 'null']));

    test('key() throws OutOfBoundsException with invalid key type', function (): void {
        $collection = new TestKeyedCollection([]);
        // Use reflection on the parent class where models is defined
        $reflection = new ReflectionClass(KeyedCollection::class);
        $modelsProperty = $reflection->getProperty('models');
        $modelsProperty->setAccessible(true);
        $modelsProperty->setValue($collection, [123 => new TupleKey('user:test', 'viewer', 'doc:1')]);

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => 'int']));

    test('key() throws OutOfBoundsException with integer key', function (): void {
        $collection = new TestKeyedCollection([]);
        // Use reflection to inject an integer key
        $reflection = new ReflectionClass(KeyedCollection::class);
        $modelsProperty = $reflection->getProperty('models');
        $modelsProperty->setAccessible(true);
        $modelsProperty->setValue($collection, [456 => new TupleKey('user:test', 'viewer', 'doc:1')]);

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => 'int']));

    test('key() throws OutOfBoundsException with numeric key', function (): void {
        $collection = new TestKeyedCollection([]);
        // Use reflection to inject a numeric key
        $reflection = new ReflectionClass(KeyedCollection::class);
        $modelsProperty = $reflection->getProperty('models');
        $modelsProperty->setAccessible(true);
        $modelsProperty->setValue($collection, [999 => new TupleKey('user:test', 'viewer', 'doc:1')]);

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => 'int']));

    test('key() throws OutOfBoundsException with boolean key', function (): void {
        $collection = new TestKeyedCollection([]);
        // Use reflection to inject a boolean key
        $reflection = new ReflectionClass(KeyedCollection::class);
        $modelsProperty = $reflection->getProperty('models');
        $modelsProperty->setAccessible(true);
        $modelsProperty->setValue($collection, [true => new TupleKey('user:test', 'viewer', 'doc:1')]);

        $collection->key();
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_KEY_TYPE, ['given' => 'int'])); // true becomes 1

    test('offsetSet() throws InvalidArgumentException with wrong value type', function (): void {
        $collection = new TestKeyedCollection([]);
        $wrongType = new stdClass;

        $collection->offsetSet('key', $wrongType);
    })->throws(ClientException::class, trans(Messages::COLLECTION_INVALID_VALUE_TYPE, ['expected' => 'OpenFGA\Models\TupleKey', 'given' => 'stdClass']));

    test('offsetSet() throws InvalidArgumentException with non-string key', function (): void {
        $collection = new TestKeyedCollection([]);
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $collection->offsetSet(123, $tuple);
    })->throws(ClientException::class, trans(Messages::COLLECTION_KEY_MUST_BE_STRING));

    test('offsetSet() sets item with string key', function (): void {
        $collection = new TestKeyedCollection([]);
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');

        $collection->offsetSet('test-key', $tuple);

        expect($collection->offsetExists('test-key'))->toBe(true);
        expect($collection->offsetGet('test-key'))->toBe($tuple);
    });

    test('offsetGet() returns null for non-existent key', function (): void {
        $collection = new TestKeyedCollection([]);

        expect($collection->offsetGet('non-existent'))->toBeNull();
    });

    test('offsetUnset() removes existing item', function (): void {
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $collection = new TestKeyedCollection(['key1' => $tuple]);

        expect($collection->offsetExists('key1'))->toBe(true);

        $collection->offsetUnset('key1');

        expect($collection->offsetExists('key1'))->toBe(false);
        expect($collection->count())->toBe(0);
    });

    test('offsetUnset() with non-existent key does nothing', function (): void {
        $collection = new TestKeyedCollection([]);

        // Should not throw exception
        $collection->offsetUnset('non-existent');

        expect($collection->count())->toBe(0);
    });

    test('toArray() filters out non-string keys', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection(['key1' => $tuple1]);

        // Manually inject a non-string key to test filtering
        $reflection = new ReflectionClass(KeyedCollection::class);
        $modelsProperty = $reflection->getProperty('models');
        $modelsProperty->setAccessible(true);
        $models = $modelsProperty->getValue($collection);
        $models[123] = $tuple2; // Non-string key
        $modelsProperty->setValue($collection, $models);

        $result = $collection->toArray();

        expect($result)->toBe(['key1' => $tuple1]);
        expect(isset($result[123]))->toBe(false);
    });

    test('jsonSerialize() returns associative array', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection([
            'key1' => $tuple1,
            'key2' => $tuple2,
        ]);

        $json = $collection->jsonSerialize();

        expect($json)->toBe([
            'key1' => $tuple1->jsonSerialize(),
            'key2' => $tuple2->jsonSerialize(),
        ]);
    });

    test('iterator position management works correctly', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection([
            'first' => $tuple1,
            'second' => $tuple2,
        ]);

        // Test iterator methods
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple1);
        expect($collection->key())->toBe('first');

        $collection->next();
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple2);
        expect($collection->key())->toBe('second');

        $collection->next();
        expect($collection->valid())->toBe(false);

        $collection->rewind();
        expect($collection->valid())->toBe(true);
        expect($collection->current())->toBe($tuple1);
        expect($collection->key())->toBe('first');
    });

    test('collection works with foreach iteration', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        $collection = new TestKeyedCollection([
            'first' => $tuple1,
            'second' => $tuple2,
        ]);

        $iterated = [];

        foreach ($collection as $key => $value) {
            $iterated[$key] = $value;
        }

        expect($iterated)->toBe(['first' => $tuple1, 'second' => $tuple2]);
    });

    test('schema() throws exception when $itemType is not defined', function (): void {
        InvalidKeyedCollection::schema();
    })->throws(SerializationException::class);

    test('schema() throws exception when $itemType is invalid', function (): void {
        InvalidTypeKeyedCollection::schema();
    })->throws(SerializationException::class);

    test('schema() returns valid schema for concrete collection', function (): void {
        $schema = TestKeyedCollection::schema();

        expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
    });

    test('schema() caches schema instances', function (): void {
        $schema1 = TestKeyedCollection::schema();
        $schema2 = TestKeyedCollection::schema();

        expect($schema1)->toBe($schema2); // Same instance
    });

    test('handles mixed associative and numeric array edge cases', function (): void {
        $tuple1 = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $tuple2 = new TupleKey(user: 'user:bob', relation: 'editor', object: 'document:2');

        // array_is_list should detect this as non-associative
        $collection = new TestKeyedCollection([0 => $tuple1, 1 => $tuple2]);

        expect($collection->has('0'))->toBe(true);
        expect($collection->has('1'))->toBe(true);
        expect($collection->get('0'))->toBe($tuple1);
        expect($collection->get('1'))->toBe($tuple2);
    });

    test('current() works with iterator position', function (): void {
        $tuple = new TupleKey(user: 'user:anne', relation: 'viewer', object: 'document:1');
        $collection = new TestKeyedCollection(['key1' => $tuple]);

        expect($collection->current())->toBe($tuple);
    });

    test('handles empty collection iteration gracefully', function (): void {
        $collection = new TestKeyedCollection([]);

        $count = 0;

        foreach ($collection as $key => $value) {
            ++$count;
        }

        expect($count)->toBe(0);
        expect($collection->valid())->toBe(false);
    });
});
