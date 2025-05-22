<?php

use OpenFGA\Models\ModelInterface;
use OpenFGA\Models\Collections\KeyedCollection;
use OpenFGA\Schema\CollectionSchemaInterface;

// DummyModel for testing (can be the same as in IndexedCollectionTest if in the same namespace or imported)
if (!class_exists('DummyModel')) {
    class DummyModel implements ModelInterface {
        public function __construct(public int $id, public ?string $key = null) {}

        public function jsonSerialize(): array {
            return ['id' => $this->id];
        }

        // A method to simulate a key, useful for KeyedCollection tests
        public function getKey(): ?string {
            return $this->key;
        }
    }
}

// ConcreteKeyedCollection for testing
class ConcreteKeyedCollection extends KeyedCollection {
    protected static string $itemType = DummyModel::class;
}

// ConcreteKeyedCollection for testing schema with missing itemType
class MisconfiguredKeyedCollection extends KeyedCollection {
}

// Tests for KeyedCollection
describe('KeyedCollection', function () {
    it('constructs with an empty array', function () {
        $collection = new ConcreteKeyedCollection([]);
        expect($collection)->toBeInstanceOf(ConcreteKeyedCollection::class)
            ->and($collection->count())->toBe(0)
            ->and($collection->isEmpty())->toBeTrue();
    });

    it('constructs with an associative array of valid items', function () {
        $item1 = new DummyModel(1, 'key1');
        $item2 = new DummyModel(2, 'key2');
        $items = ['key1' => $item1, 'key2' => $item2];
        $collection = new ConcreteKeyedCollection($items);
        expect($collection->count())->toBe(2)
            ->and($collection->get('key1'))->toBe($item1)
            ->and($collection->get('key2'))->toBe($item2);
    });

    it('constructs with a numerically indexed array of valid items (keys become strings)', function () {
        $item1 = new DummyModel(1); // No explicit key, but array key is 0
        $item2 = new DummyModel(2); // No explicit key, but array key is 1
        $items = [$item1, $item2];
        $collection = new ConcreteKeyedCollection($items);
        expect($collection->count())->toBe(2)
            ->and($collection->get('0'))->toBe($item1) // Pest might need string key here
            ->and($collection->get('1'))->toBe($item2);
    });

    it('throws TypeError if a non-ModelInterface item is provided in the input array', function (array $items) {
        new ConcreteKeyedCollection($items);
    })->with([
        [['key1' => new DummyModel(1), 'key2' => new stdClass()]],
        [[new DummyModel(1), new stdClass()]],
    ])->throws(TypeError::class);

    it('adds a valid item with a string key', function () {
        $collection = new ConcreteKeyedCollection([]);
        $item = new DummyModel(1);
        $collection->add($item, 'newKey');
        expect($collection->count())->toBe(1)
            ->and($collection->get('newKey'))->toBe($item)
            ->and($collection->has('newKey'))->toBeTrue();
    });

    it('throws TypeError when adding an item of the wrong type', function () {
        $collection = new ConcreteKeyedCollection([]);
        $collection->add(new stdClass(), 'key1');
    })->throws(TypeError::class);

    it('returns the correct count of items', function () {
        $collection = new ConcreteKeyedCollection(['key1' => new DummyModel(1), 'key2' => new DummyModel(2)]);
        expect($collection->count())->toBe(2);
    });

    describe('iteration', function () {
        it('iterates an empty collection', function () {
            $collection = new ConcreteKeyedCollection([]);
            $count = 0;
            foreach ($collection as $key => $item) {
                $count++;
            }
            expect($count)->toBe(0)
                ->and($collection->current())->toBeNull()
                ->and($collection->key())->toBeNull()
                ->and($collection->valid())->toBeFalse();
        });

        it('iterates a collection with items and ensures keys are strings', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteKeyedCollection(['keyA' => $item1, '0' => $item2]); // Mixed keys
            $iterations = 0;
            $keys = [];
            $values = [];

            $collection->rewind();
            while ($collection->valid()) {
                $currentKey = $collection->key();
                expect($currentKey)->toBeString();
                $keys[] = $currentKey;
                $values[] = $collection->current();
                $iterations++;
                $collection->next();
            }

            expect($iterations)->toBe(2)
                ->and($keys)->toEqual(['keyA', '0']) // Order might depend on internal array handling
                ->and($values)->toContain($item1, $item2)
                ->and(array_values($collection->toArray()))->toContain($item1, $item2);


            // Test foreach directly
            $iterationsForeach = 0;
            $itemsForeach = [];
            foreach ($collection as $key => $value) {
                expect($key)->toBeString();
                $itemsForeach[$key] = $value;
                $iterationsForeach++;
            }
            expect($iterationsForeach)->toBe(2)
                ->and($itemsForeach)->toHaveKey('keyA', $item1)
                ->and($itemsForeach)->toHaveKey('0', $item2);
        });
    });

    describe('get()', function () {
        it('gets an existing item by key', function () {
            $item1 = new DummyModel(1);
            $collection = new ConcreteKeyedCollection(['key1' => $item1]);
            expect($collection->get('key1'))->toBe($item1);
        });

        it('returns null for a non-existing item by key', function () {
            $collection = new ConcreteKeyedCollection([]);
            expect($collection->get('nonExistentKey'))->toBeNull();
        });
    });

    describe('has()', function () {
        it('returns true for an existing key', function () {
            $collection = new ConcreteKeyedCollection(['key1' => new DummyModel(1)]);
            expect($collection->has('key1'))->toBeTrue();
        });

        it('returns false for a non-existing key', function () {
            $collection = new ConcreteKeyedCollection([]);
            expect($collection->has('nonExistentKey'))->toBeFalse();
        });
    });

    describe('jsonSerialize()', function () {
        it('returns an empty object (stdClass) for an empty collection', function () {
            $collection = new ConcreteKeyedCollection([]);
            $json = $collection->jsonSerialize();
            expect($json)->toBeInstanceOf(stdClass::class) // Empty assoc array becomes stdClass
                ->and((array)$json)->toBe([]);
        });

        it('returns an object with serialized items for a non-empty collection', function () {
            $collection = new ConcreteKeyedCollection([
                'itemA' => new DummyModel(1),
                'itemB' => new DummyModel(2)
            ]);
            $expectedJson = [
                'itemA' => ['id' => 1],
                'itemB' => ['id' => 2]
            ];
            expect($collection->jsonSerialize())->toEqual((object)$expectedJson);
        });
    });

    describe('ArrayAccess', function () {
        it('offsetExists checks existing and non-existing string keys', function () {
            $collection = new ConcreteKeyedCollection(['key1' => new DummyModel(1)]);
            expect(isset($collection['key1']))->toBeTrue()
                ->and(isset($collection['nonExistentKey']))->toBeFalse();
        });

        it('offsetGet gets existing and non-existing string keys', function () {
            $item1 = new DummyModel(1);
            $collection = new ConcreteKeyedCollection(['key1' => $item1]);
            expect($collection['key1'])->toBe($item1)
                ->and($collection['nonExistentKey'])->toBeNull();
        });

        it('offsetSet sets with a string offset', function () {
            $collection = new ConcreteKeyedCollection([]);
            $item1 = new DummyModel(1);
            $collection['newKey'] = $item1;
            expect($collection->count())->toBe(1)
                ->and($collection['newKey'])->toBe($item1);
        });

        it('offsetSet throws TypeError for wrong item type', function (mixed $value) {
            $collection = new ConcreteKeyedCollection([]);
            $collection['aKey'] = $value;
        })->with([
            [new stdClass()],
            ['not a model'],
            [123],
        ])->throws(TypeError::class);

        it('offsetSet throws InvalidArgumentException if offset is not a string or null (for append, though KeyedCollection may not support null offset for append)', function (mixed $offset) {
            $collection = new ConcreteKeyedCollection([]);
            $collection[$offset] = new DummyModel(1);
        })->with([
            [123], // Integer offset
            [[]],   // Array offset
            [new stdClass()], // Object offset
        ])->throws(InvalidArgumentException::class, 'KeyedCollection keys must be strings.');

        it('offsetSet with null offset throws InvalidArgumentException', function () {
            $collection = new ConcreteKeyedCollection([]);
            $collection[] = new DummyModel(1);
        })->throws(InvalidArgumentException::class, 'KeyedCollection keys must be strings. Null offset is not supported for append.');


        it('offsetUnset unsets an existing key', function () {
            $collection = new ConcreteKeyedCollection(['key1' => new DummyModel(1), 'key2' => new DummyModel(2)]);
            unset($collection['key1']);
            expect($collection->count())->toBe(1)
                ->and($collection->has('key1'))->toBeFalse()
                ->and($collection->has('key2'))->toBeTrue();
        });

        it('offsetUnset does nothing for a non-existing key', function () {
            $collection = new ConcreteKeyedCollection(['key1' => new DummyModel(1)]);
            unset($collection['nonExistentKey']);
            expect($collection->count())->toBe(1);
        });
    });

    it('toArray returns the internal associative array of models', function () {
        $items = ['key1' => new DummyModel(1), 'key2' => new DummyModel(2)];
        $collection = new ConcreteKeyedCollection($items);
        expect($collection->toArray())->toBe($items);
    });

    describe('schema()', function () {
        it('returns a CollectionSchemaInterface instance', function () {
            $schema = ConcreteKeyedCollection::schema();
            expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        });

        it('has correct className and itemType in the schema', function () {
            $schema = ConcreteKeyedCollection::schema();
            expect($schema->getClassName())->toBe(ConcreteKeyedCollection::class)
                ->and($schema->getItemType())->toBe(DummyModel::class);
        });

        it('throws TypeError if itemType is not defined', function () {
            MisconfiguredKeyedCollection::schema();
        })->throws(TypeError::class, 'must be defined and be a subclass of');

        it('throws TypeError if itemType is not a ModelInterface', function () {
            class InvalidItemTypeKeyedCollection extends KeyedCollection {
                protected static string $itemType = stdClass::class;
            }
            InvalidItemTypeKeyedCollection::schema();
        })->throws(TypeError::class, 'must be a subclass of');
    });

    // Test for constructor throwing TypeError when $itemType is not defined
    // As with IndexedCollection, this is primarily tested via the schema() method
    // for the static $itemType property and through item validation on construction/add.
});

?>
