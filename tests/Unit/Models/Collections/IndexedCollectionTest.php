<?php

use OpenFGA\Models\ModelInterface;
use OpenFGA\Models\Collections\IndexedCollection;
use OpenFGA\Schema\CollectionSchemaInterface;

// DummyModel for testing
class DummyModel implements ModelInterface {
    public function __construct(public int $id) {}

    public function jsonSerialize(): array {
        return ['id' => $this->id];
    }
}

// ConcreteIndexedCollection for testing
class ConcreteIndexedCollection extends IndexedCollection {
    protected static string $itemType = DummyModel::class;
}

// ConcreteIndexedCollection for testing schema with missing itemType
class MisconfiguredIndexedCollection extends IndexedCollection {
}

// Tests for IndexedCollection
describe('IndexedCollection', function () {
    it('constructs with no arguments', function () {
        $collection = new ConcreteIndexedCollection();
        expect($collection)->toBeInstanceOf(ConcreteIndexedCollection::class)
            ->and($collection->count())->toBe(0);
    });

    it('constructs with a single item', function () {
        $item = new DummyModel(1);
        $collection = new ConcreteIndexedCollection($item);
        expect($collection->count())->toBe(1)
            ->and($collection->get(0))->toBe($item);
    });

    it('constructs with multiple items as arguments', function () {
        $item1 = new DummyModel(1);
        $item2 = new DummyModel(2);
        $collection = new ConcreteIndexedCollection($item1, $item2);
        expect($collection->count())->toBe(2)
            ->and($collection->get(0))->toBe($item1)
            ->and($collection->get(1))->toBe($item2);
    });

    it('constructs with an array of items', function () {
        $items = [new DummyModel(1), new DummyModel(2)];
        $collection = new ConcreteIndexedCollection($items);
        expect($collection->count())->toBe(2)
            ->and($collection->get(0))->toBe($items[0])
            ->and($collection->get(1))->toBe($items[1]);
    });

    it('throws TypeError if a non-ModelInterface item is added during construction with single item', function () {
        new ConcreteIndexedCollection(new stdClass());
    })->throws(TypeError::class);

    it('throws TypeError if a non-ModelInterface item is added during construction with multiple items', function () {
        new ConcreteIndexedCollection(new DummyModel(1), new stdClass());
    })->throws(TypeError::class);

    it('throws TypeError if a non-ModelInterface item is added during construction with an array', function () {
        new ConcreteIndexedCollection([new DummyModel(1), new stdClass()]);
    })->throws(TypeError::class);

    it('adds a valid item', function () {
        $collection = new ConcreteIndexedCollection();
        $item = new DummyModel(1);
        $collection->add($item);
        expect($collection->count())->toBe(1)
            ->and($collection->get(0))->toBe($item);
    });

    it('throws TypeError when adding an item of the wrong type', function () {
        $collection = new ConcreteIndexedCollection();
        $collection->add(new stdClass());
    })->throws(TypeError::class);

    it('clears the collection', function () {
        $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
        $collection->clear();
        expect($collection->count())->toBe(0)
            ->and($collection->isEmpty())->toBeTrue();
    });

    it('returns the correct count of items', function () {
        $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
        expect($collection->count())->toBe(2);
    });

    describe('iteration', function () {
        it('iterates an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            $count = 0;
            foreach ($collection as $item) {
                $count++;
            }
            expect($count)->toBe(0)
                ->and($collection->current())->toBeNull()
                ->and($collection->key())->toBeNull()
                ->and($collection->valid())->toBeFalse();
        });

        it('iterates a collection with items', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1, $item2);
            $iterations = 0;
            $keys = [];
            $values = [];

            $collection->rewind();
            while ($collection->valid()) {
                $keys[] = $collection->key();
                $values[] = $collection->current();
                $iterations++;
                $collection->next();
            }

            expect($iterations)->toBe(2)
                ->and($keys)->toBe([0, 1])
                ->and($values)->toBe([$item1, $item2])
                ->and($collection->current())->toBeNull()
                ->and($collection->key())->toBeNull()
                ->and($collection->valid())->toBeFalse();

            // Test foreach directly
            $iterationsForeach = 0;
            $itemsForeach = [];
            foreach ($collection as $key => $value) {
                $itemsForeach[$key] = $value;
                $iterationsForeach++;
            }
            expect($iterationsForeach)->toBe(2)
                ->and($itemsForeach)->toBe([0 => $item1, 1 => $item2]);
        });
    });

    describe('every()', function () {
        it('returns true with a callback that returns true for all items', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $result = $collection->every(fn(DummyModel $model) => $model->id > 0);
            expect($result)->toBeTrue();
        });

        it('returns false with a callback that returns false for some items', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $result = $collection->every(fn(DummyModel $model) => $model->id > 1);
            expect($result)->toBeFalse();
        });

        it('returns true on an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            $result = $collection->every(fn(DummyModel $model) => false);
            expect($result)->toBeTrue();
        });
    });

    describe('filter()', function () {
        it('filters with a callback that keeps all items', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1, $item2);
            $filtered = $collection->filter(fn(DummyModel $model) => $model->id > 0);
            expect($filtered)->toBeInstanceOf(ConcreteIndexedCollection::class)
                ->and($filtered->count())->toBe(2)
                ->and($filtered->toArray())->toBe([$item1, $item2]);
        });

        it('filters with a callback that keeps some items', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1, $item2);
            $filtered = $collection->filter(fn(DummyModel $model) => $model->id > 1);
            expect($filtered->count())->toBe(1)
                ->and($filtered->get(0))->toBe($item2);
        });

        it('filters with a callback that keeps no items', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $filtered = $collection->filter(fn(DummyModel $model) => $model->id > 2);
            expect($filtered->count())->toBe(0)
                ->and($filtered->isEmpty())->toBeTrue();
        });
    });

    describe('first()', function () {
        it('returns null on an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            expect($collection->first())->toBeNull();
        });

        it('returns the first item on a non-empty collection without a callback', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1, $item2);
            expect($collection->first())->toBe($item1);
        });

        it('returns the first matching item with a callback', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1, $item2);
            $found = $collection->first(fn(DummyModel $model) => $model->id === 2);
            expect($found)->toBe($item2);
        });

        it('returns null if no item matches the callback', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $found = $collection->first(fn(DummyModel $model) => $model->id === 3);
            expect($found)->toBeNull();
        });
    });

    describe('get()', function () {
        it('gets an existing item by offset', function () {
            $item1 = new DummyModel(1);
            $collection = new ConcreteIndexedCollection($item1);
            expect($collection->get(0))->toBe($item1);
        });

        it('returns null for a non-existing item by offset', function () {
            $collection = new ConcreteIndexedCollection();
            expect($collection->get(0))->toBeNull();
        });
    });

    describe('isEmpty()', function () {
        it('returns true on an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            expect($collection->isEmpty())->toBeTrue();
        });

        it('returns false on a non-empty collection', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1));
            expect($collection->isEmpty())->toBeFalse();
        });
    });

    describe('jsonSerialize()', function () {
        it('returns an empty array for an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            expect($collection->jsonSerialize())->toBe([]);
        });

        it('returns an array of serialized items for a non-empty collection', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            expect($collection->jsonSerialize())->toBe([['id' => 1], ['id' => 2]]);
        });
    });

    describe('ArrayAccess', function () {
        it('offsetExists checks existing and non-existing offsets', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1));
            expect(isset($collection[0]))->toBeTrue()
                ->and(isset($collection[1]))->toBeFalse();
        });

        it('offsetGet gets existing and non-existing offsets', function () {
            $item1 = new DummyModel(1);
            $collection = new ConcreteIndexedCollection($item1);
            expect($collection[0])->toBe($item1)
                ->and($collection[1])->toBeNull();
        });

        it('offsetSet sets with null offset (appends)', function () {
            $collection = new ConcreteIndexedCollection();
            $item1 = new DummyModel(1);
            $collection[] = $item1;
            expect($collection->count())->toBe(1)
                ->and($collection[0])->toBe($item1);
        });

        it('offsetSet sets with an existing offset (overwrites)', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $collection = new ConcreteIndexedCollection($item1);
            $collection[0] = $item2;
            expect($collection->count())->toBe(1)
                ->and($collection[0])->toBe($item2);
        });

        it('offsetSet throws TypeError for wrong item type', function (mixed $value) {
            $collection = new ConcreteIndexedCollection();
            $collection[] = $value;
        })->with([
            [new stdClass()],
            ['not a model'],
            [123],
        ])->throws(TypeError::class);

        it('offsetUnset unsets an existing offset and re-indexes', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $item3 = new DummyModel(3);
            $collection = new ConcreteIndexedCollection($item1, $item2, $item3);
            unset($collection[1]);
            expect($collection->count())->toBe(2)
                ->and($collection[0])->toBe($item1)
                ->and($collection[1])->toBe($item3) // Check re-indexing
                ->and(isset($collection[2]))->toBeFalse();
        });

        it('offsetUnset does nothing for a non-existing offset', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1));
            unset($collection[1]);
            expect($collection->count())->toBe(1);
        });
    });

    describe('reduce()', function () {
        it('reduces with an initial value and a callback', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2), new DummyModel(3));
            $sum = $collection->reduce(function (int $carry, DummyModel $model) {
                return $carry + $model->id;
            }, 10);
            expect($sum)->toBe(10 + 1 + 2 + 3);
        });

        it('reduces on an empty collection returns initial', function () {
            $collection = new ConcreteIndexedCollection();
            $result = $collection->reduce(fn($carry, $item) => $carry, 'initial');
            expect($result)->toBe('initial');
        });
    });

    describe('some()', function () {
        it('returns true with a callback that returns true for some items', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $result = $collection->some(fn(DummyModel $model) => $model->id === 1);
            expect($result)->toBeTrue();
        });

        it('returns false with a callback that returns false for all items', function () {
            $collection = new ConcreteIndexedCollection(new DummyModel(1), new DummyModel(2));
            $result = $collection->some(fn(DummyModel $model) => $model->id === 3);
            expect($result)->toBeFalse();
        });

        it('returns false on an empty collection', function () {
            $collection = new ConcreteIndexedCollection();
            $result = $collection->some(fn(DummyModel $model) => true);
            expect($result)->toBeFalse();
        });
    });

    it('toArray returns the internal array of models', function () {
        $items = [new DummyModel(1), new DummyModel(2)];
        $collection = new ConcreteIndexedCollection($items);
        expect($collection->toArray())->toBe($items);
    });

    describe('withItems()', function () {
        it('returns a new instance', function () {
            $original = new ConcreteIndexedCollection();
            $newCollection = $original->withItems(new DummyModel(1));
            expect($newCollection)->not->toBe($original)
                ->and($newCollection)->toBeInstanceOf(ConcreteIndexedCollection::class);
        });

        it('contains original and added items', function () {
            $item1 = new DummyModel(1);
            $item2 = new DummyModel(2);
            $item3 = new DummyModel(3);
            $original = new ConcreteIndexedCollection($item1);
            $newCollection = $original->withItems($item2, $item3);

            expect($original->count())->toBe(1) // Original is unchanged
                ->and($newCollection->count())->toBe(3)
                ->and($newCollection->toArray())->toBe([$item1, $item2, $item3]);

            $newCollectionFromArray = $original->withItems([$item2, $item3]);
            expect($newCollectionFromArray->count())->toBe(3)
                ->and($newCollectionFromArray->toArray())->toBe([$item1, $item2, $item3]);
        });

        it('throws TypeError if a non-ModelInterface item is added', function () {
            $collection = new ConcreteIndexedCollection();
            $collection->withItems(new stdClass());
        })->throws(TypeError::class);
    });

    describe('schema()', function () {
        it('returns a CollectionSchemaInterface instance', function () {
            $schema = ConcreteIndexedCollection::schema();
            expect($schema)->toBeInstanceOf(CollectionSchemaInterface::class);
        });

        it('has correct className and itemType in the schema', function () {
            $schema = ConcreteIndexedCollection::schema();
            expect($schema->getClassName())->toBe(ConcreteIndexedCollection::class)
                ->and($schema->getItemType())->toBe(DummyModel::class);
        });

        it('throws TypeError if itemType is not defined', function () {
            // This test relies on a separate misconfigured class
            MisconfiguredIndexedCollection::schema();
        })->throws(TypeError::class, 'must be defined and be a subclass of');

        it('throws TypeError if itemType is not a ModelInterface', function () {
            // This test relies on a separate misconfigured class
            class InvalidItemTypeIndexedCollection extends IndexedCollection {
                protected static string $itemType = stdClass::class;
            }
            InvalidItemTypeIndexedCollection::schema();
        })->throws(TypeError::class, 'must be a subclass of');
    });

    // Test for constructor throwing TypeError when $itemType is not defined
    // This is tricky because the abstract class itself can't be instantiated directly
    // if $itemType is checked in the constructor of the abstract class.
    // However, the check is in `validateItem` which is called by `add` and constructor.
    // If the abstract constructor itself tries to use $itemType, we might need a different approach.
    // For now, the existing constructor tests cover adding invalid items.
    // The schema test for MisconfiguredIndexedCollection checks the static $itemType.
});

?>
