<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionTupleKey, Assertions, TupleKey, TupleKeys};

beforeEach(function (): void {
    $this->tupleKey1 = new AssertionTupleKey('user:1', 'reader', 'document:1');
    $this->tupleKey2 = new AssertionTupleKey('user:2', 'writer', 'document:2');
    $this->assertion1 = new Assertion($this->tupleKey1, true);
    $this->assertion2 = new Assertion($this->tupleKey2, false);
});

test('can add assertions', function (): void {
    $assertions = new Assertions();
    $assertions->add($this->assertion1);

    expect($assertions)->toHaveCount(1)
        ->and($assertions[0])->toBe($this->assertion1);
});

test('can be converted to array', function (): void {
    $contextualTuples = new TupleKeys([
        new TupleKey('user:3', 'admin', 'document:3'),
    ]);

    $assertion2 = new Assertion($this->tupleKey2, false, $contextualTuples, ['key' => 'value']);

    $assertions = new Assertions([$this->assertion1, $assertion2]);
    $array = $assertions->jsonSerialize();

    expect($array)->toBeArray()
        ->toHaveCount(2)
        ->and($array[0])->toMatchArray([
            'tuple_key' => $this->tupleKey1->jsonSerialize(),
            'expectation' => true,
        ])
        ->and($array[0])->not->toHaveKey('contextual_tuples')
        ->and($array[0])->not->toHaveKey('context')
        ->and($array[1])->toMatchArray([
            'tuple_key' => $this->tupleKey2->jsonSerialize(),
            'expectation' => false,
            'context' => ['key' => 'value'],
        ])
        ->and($array[1])->toHaveKey('contextual_tuples')
        ->and($array[1]['contextual_tuples'])->toBeArray()
        ->toHaveCount(1);
});

test('can be initialized empty', function (): void {
    $assertions = new Assertions();
    expect($assertions)->toHaveCount(0);
});

test('can be initialized with array of assertions', function (): void {
    $assertions = new Assertions([$this->assertion1, $this->assertion2]);

    expect($assertions)->toHaveCount(2)
        ->and($assertions[0])->toBe($this->assertion1)
        ->and($assertions[1])->toBe($this->assertion2);
});

test('can be iterated over', function (): void {
    $assertions = new Assertions([$this->assertion1, $this->assertion2]);
    $collected = [];

    foreach ($assertions as $assertion) {
        $collected[] = $assertion;
    }

    expect($collected)->toHaveCount(2)
        ->and($collected[0])->toBe($this->assertion1)
        ->and($collected[1])->toBe($this->assertion2);
});

test('valid returns false for empty collection', function (): void {
    $assertions = new Assertions();
    expect($assertions->valid())->toBeFalse();
});

test('validates array item types', function (): void {
    // @phpstan-ignore-next-line - Intentionally passing wrong type for testing
    expect(fn () => new Assertions(['invalid']))->toThrow(TypeError::class);
});

test('validates item types', function (): void {
    // @phpstan-ignore-next-line - Intentionally passing wrong type for testing
    expect(fn () => new Assertions('invalid'))->toThrow(TypeError::class);
});

test('can be initialized with iterable', function (): void {
    $iterable = new class($this->assertion1) implements IteratorAggregate {
        public function __construct(private $assertion)
        {
        }

        public function getIterator(): Traversable
        {
            yield $this->assertion;
        }
    };

    $assertions = new Assertions($iterable);

    expect($assertions)->toHaveCount(1)
        ->and($assertions[0])->toBe($this->assertion1);
});
