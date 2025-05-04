<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use InvalidArgumentException;
use OpenFGA\Models\Model;
use OpenFGA\Models\ModelCollection;
use OpenFGA\Models\ModelInterface;

class TestModel extends Model implements ModelInterface
{
    public function __construct(
        public string $property1 = 'test1',
        public int $property2 = 123
    ) {}

    public function toArray(): array
    {
        return [
            'property1' => $this->property1,
            'property2' => $this->property2,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            property1: $data['property1'] ?? 'default1',
            property2: $data['property2'] ?? 0
        );
    }
}

class AnotherTestModel extends Model implements ModelInterface
{
    public function __construct(
        public string $name = 'test'
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'] ?? 'default'
        );
    }
}

class TestModelCollection extends ModelCollection
{
    public function add(ModelInterface $model): void
    {
        $this->models[] = $model;
    }

    public function current(): ModelInterface
    {
        return $this->models[$this->key()];
    }

    public function offsetGet(mixed $offset): ?ModelInterface
    {
        return $this->models[$offset] ?? null;
    }

    public static function fromArray(array $data): self
    {
        $collection = new self();

        foreach ($data as $item) {
            $collection->add(TestModel::fromArray($item));
        }

        return $collection;
    }
}

it('can create a concrete Model implementation', function () {
    $model = new TestModel('value1', 456);

    expect($model)->toBeInstanceOf(Model::class)
        ->and($model)->toBeInstanceOf(ModelInterface::class)
        ->and($model->property1)->toBe('value1')
        ->and($model->property2)->toBe(456);
});

it('can convert Model to array', function () {
    $model = new TestModel('value1', 456);
    $array = $model->toArray();

    expect($array)->toBeArray()
        ->and($array)->toBe([
            'property1' => 'value1',
            'property2' => 456,
        ]);
});

it('can create Model from array', function () {
    $data = [
        'property1' => 'value1',
        'property2' => 456,
    ];

    $model = TestModel::fromArray($data);

    expect($model)->toBeInstanceOf(TestModel::class)
        ->and($model->property1)->toBe('value1')
        ->and($model->property2)->toBe(456);
});

it('properly implements JsonSerializable interface', function () {
    $model = new TestModel('value1', 456);
    $json = json_encode($model);
    $decoded = json_decode($json, true);

    expect($decoded)->toBe([
        'property1' => 'value1',
        'property2' => 456,
    ]);
});

it('can create a concrete ModelCollection implementation', function () {
    $collection = new TestModelCollection();

    expect($collection)->toBeInstanceOf(ModelCollection::class);
});

it('can add Models to collection', function () {
    $collection = new TestModelCollection();
    $model1 = new TestModel('value1', 123);
    $model2 = new TestModel('value2', 456);

    $collection->add($model1);
    $collection->add($model2);

    expect($collection)->toHaveCount(2);
});

it('implements the Iterator interface', function () {
    $collection = new TestModelCollection();
    $model1 = new TestModel('value1', 123);
    $model2 = new TestModel('value2', 456);

    $collection->add($model1);
    $collection->add($model2);

    // Test iterator by manual iteration
    $collection->rewind();
    expect($collection->valid())->toBeTrue();

    if ($collection->valid()) {
        expect($collection->key())->toBe(0)
            ->and($collection->current())->toBe($model1);
        $collection->next();
    }

    expect($collection->valid())->toBeTrue();

    if ($collection->valid()) {
        expect($collection->key())->toBe(1)
            ->and($collection->current())->toBe($model2);
        $collection->next();
    }

    expect($collection->valid())->toBeFalse();

    // Test iterator with foreach
    $items = [];
    foreach ($collection as $key => $item) {
        $items[$key] = $item;
    }

    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($model1)
        ->and($items[1])->toBe($model2);
});

it('implements the ArrayAccess interface', function () {
    $collection = new TestModelCollection();
    $model1 = new TestModel('value1', 123);
    $model2 = new TestModel('value2', 456);

    $collection->add($model1);

    expect($collection->offsetExists(0))->toBeTrue()
        ->and($collection->offsetExists(1))->toBeFalse()
        ->and($collection->offsetGet(0))->toBe($model1)
        ->and($collection->offsetGet(1))->toBeNull();

    $collection->offsetSet(1, $model2);
    expect($collection->offsetExists(1))->toBeTrue()
        ->and($collection->offsetGet(1))->toBe($model2);

    $collection->offsetUnset(0);
    expect($collection->offsetExists(0))->toBeFalse();
});

it('implements the Countable interface', function () {
    $collection = new TestModelCollection();

    expect($collection)->toHaveCount(0);

    $model1 = new TestModel('value1', 123);
    $model2 = new TestModel('value2', 456);
    $model3 = new TestModel('value3', 789);

    $collection->add($model1);
    expect($collection)->toHaveCount(1);

    $collection->add($model2);
    $collection->add($model3);
    expect($collection)->toHaveCount(3);

    $collection->offsetUnset(0);
    expect($collection)->toHaveCount(2);
});

it('can convert ModelCollection to array of model arrays', function () {
    $collection = new TestModelCollection();
    $model1 = new TestModel('value1', 123);
    $model2 = new TestModel('value2', 456);

    $collection->add($model1);
    $collection->add($model2);

    $array = $collection->toArray();

    expect($array)->toBe([
        [
            'property1' => 'value1',
            'property2' => 123,
        ],
        [
            'property1' => 'value2',
            'property2' => 456,
        ],
    ]);
});

it('creates ModelCollection from array', function () {
    $data = [
        [
            'property1' => 'value1',
            'property2' => 123,
        ],
        [
            'property1' => 'value2',
            'property2' => 456,
        ],
    ];

    $collection = TestModelCollection::fromArray($data);

    expect($collection)->toHaveCount(2)
        ->and($collection[0]->property1)->toBe('value1')
        ->and($collection[0]->property2)->toBe(123)
        ->and($collection[1]->property1)->toBe('value2')
        ->and($collection[1]->property2)->toBe(456);
});

it('throws exception when adding non-ModelInterface to collection', function () {
    $collection = new TestModelCollection();
    $notAModel = new \stdClass();

    expect(fn() => $collection->offsetSet(null, $notAModel))
        ->toThrow(InvalidArgumentException::class, 'Must be an Model instance');
});

it('can add model to collection with null offset', function () {
    $collection = new TestModelCollection();
    $model = new TestModel('null-offset-test', 789);

    $collection->offsetSet(null, $model);

    expect($collection)->toHaveCount(1);

    $collection->rewind();
    expect($collection->valid())->toBeTrue();
    expect($collection->current())->toBe($model);
});
