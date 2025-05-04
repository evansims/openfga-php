<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{Assertion, Assertions, AssertionInterface, AssertionsInterface, AssertionTupleKey, TupleKeys, TupleKey};

it('can create an AssertionTupleKey instance with constructor', function () {
    $user = 'user:anne';
    $relation = 'reader';
    $object = 'document:budget';
    
    $tupleKey = new AssertionTupleKey($user, $relation, $object);
    
    expect($tupleKey->user)->toBe($user)
        ->and($tupleKey->relation)->toBe($relation)
        ->and($tupleKey->object)->toBe($object);
});

it('can convert AssertionTupleKey to array', function () {
    $user = 'user:anne';
    $relation = 'reader';
    $object = 'document:budget';
    
    $tupleKey = new AssertionTupleKey($user, $relation, $object);
    $array = $tupleKey->toArray();
    
    expect($array)->toBeArray()
        ->and($array['user'])->toBe($user)
        ->and($array['relation'])->toBe($relation)
        ->and($array['object'])->toBe($object);
});

it('can create AssertionTupleKey from array', function () {
    $data = [
        'user' => 'user:anne',
        'relation' => 'reader',
        'object' => 'document:budget',
    ];
    
    $tupleKey = AssertionTupleKey::fromArray($data);
    
    expect($tupleKey->user)->toBe($data['user'])
        ->and($tupleKey->relation)->toBe($data['relation'])
        ->and($tupleKey->object)->toBe($data['object']);
});

it('can create an Assertion instance with constructor', function () {
    $tupleKey = new AssertionTupleKey('user:anne', 'reader', 'document:budget');
    $expectation = true;
    
    $assertion = new Assertion($tupleKey, $expectation);
    
    expect($assertion)->toBeInstanceOf(AssertionInterface::class)
        ->and($assertion->tupleKey)->toBe($tupleKey)
        ->and($assertion->expectation)->toBe($expectation)
        ->and($assertion->contextualTuples)->toBeNull()
        ->and($assertion->context)->toBeNull();
});

it('can create an Assertion instance with optional parameters', function () {
    $tupleKey = new AssertionTupleKey('user:anne', 'reader', 'document:budget');
    $expectation = true;
    
    $tupleKey1 = new TupleKey('user:anne', 'reader', 'document:budget');
    $tupleKey2 = new TupleKey('user:bob', 'writer', 'document:budget');
    $contextualTuples = new TupleKeys();
    $contextualTuples->add($tupleKey1);
    $contextualTuples->add($tupleKey2);
    
    $context = ['tenant' => 'acme'];
    
    $assertion = new Assertion($tupleKey, $expectation, $contextualTuples, $context);
    
    expect($assertion->contextualTuples)->toBe($contextualTuples)
        ->and($assertion->context)->toBe($context);
});

it('can convert Assertion to array', function () {
    $tupleKey = new AssertionTupleKey('user:anne', 'reader', 'document:budget');
    $expectation = true;
    
    $assertion = new Assertion($tupleKey, $expectation);
    $array = $assertion->toArray();
    
    expect($array)->toBeArray()
        ->and($array['tuple_key'])->toBe($tupleKey->toArray())
        ->and($array['expectation'])->toBe($expectation)
        ->and($array['contextual_tuples'])->toBeNull()
        ->and($array['context'])->toBeNull();
});

it('can convert Assertion with optional parameters to array', function () {
    $tupleKey = new AssertionTupleKey('user:anne', 'reader', 'document:budget');
    $expectation = true;
    
    $tupleKey1 = new TupleKey('user:anne', 'reader', 'document:budget');
    $tupleKey2 = new TupleKey('user:bob', 'writer', 'document:budget');
    $contextualTuples = new TupleKeys();
    $contextualTuples->add($tupleKey1);
    $contextualTuples->add($tupleKey2);
    
    $context = ['tenant' => 'acme'];
    
    $assertion = new Assertion($tupleKey, $expectation, $contextualTuples, $context);
    $array = $assertion->toArray();
    
    expect($array['contextual_tuples'])->not->toBeNull()
        ->and($array['context'])->toBe($context);
});

it('can create Assertion from array', function () {
    $data = [
        'tuple_key' => [
            'user' => 'user:anne',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
        'expectation' => true,
    ];
    
    $assertion = Assertion::fromArray($data);
    
    expect($assertion)->toBeInstanceOf(AssertionInterface::class)
        ->and($assertion->tupleKey->user)->toBe($data['tuple_key']['user'])
        ->and($assertion->tupleKey->relation)->toBe($data['tuple_key']['relation'])
        ->and($assertion->tupleKey->object)->toBe($data['tuple_key']['object'])
        ->and($assertion->expectation)->toBe($data['expectation'])
        ->and($assertion->contextualTuples)->toBeNull()
        ->and($assertion->context)->toBeNull();
});

it('can create Assertion from array with optional parameters', function () {
    $data = [
        'tuple_key' => [
            'user' => 'user:anne',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
        'expectation' => true,
        'contextual_tuples' => [
            [
                'user' => 'user:anne',
                'relation' => 'reader',
                'object' => 'document:budget',
            ],
            [
                'user' => 'user:bob',
                'relation' => 'writer',
                'object' => 'document:budget',
            ],
        ],
        'context' => ['tenant' => 'acme'],
    ];
    
    $assertion = Assertion::fromArray($data);
    
    expect($assertion->contextualTuples)->not->toBeNull()
        ->and($assertion->contextualTuples)->toHaveCount(2)
        ->and($assertion->context)->toBe($data['context']);
});

it('can create an empty Assertions collection', function () {
    $assertions = new Assertions();
    
    expect($assertions)->toBeInstanceOf(AssertionsInterface::class)
        ->and($assertions)->toHaveCount(0);
});

it('can add Assertion to Assertions collection', function () {
    $assertion1 = new Assertion(
        new AssertionTupleKey('user:anne', 'reader', 'document:budget'),
        true
    );
    
    $assertion2 = new Assertion(
        new AssertionTupleKey('user:bob', 'writer', 'document:budget'),
        false
    );
    
    $assertions = new Assertions();
    $assertions->add($assertion1);
    $assertions->add($assertion2);
    
    expect($assertions)->toHaveCount(2);
});

it('can get current Assertion from Assertions collection', function () {
    $assertion = new Assertion(
        new AssertionTupleKey('user:anne', 'reader', 'document:budget'),
        true
    );
    
    $assertions = new Assertions();
    $assertions->add($assertion);
    
    expect($assertions->current())->toBe($assertion);
});

it('can get Assertion by offset from Assertions collection', function () {
    $assertion1 = new Assertion(
        new AssertionTupleKey('user:anne', 'reader', 'document:budget'),
        true
    );
    
    $assertion2 = new Assertion(
        new AssertionTupleKey('user:bob', 'writer', 'document:budget'),
        false
    );
    
    $assertions = new Assertions();
    $assertions->add($assertion1);
    $assertions->add($assertion2);
    
    expect($assertions->offsetGet(0))->toBe($assertion1)
        ->and($assertions->offsetGet(1))->toBe($assertion2)
        ->and($assertions->offsetGet(2))->toBeNull();
});

it('can create Assertions collection from array', function () {
    $data = [
        [
            'tuple_key' => [
                'user' => 'user:anne',
                'relation' => 'reader',
                'object' => 'document:budget',
            ],
            'expectation' => true,
        ],
        [
            'tuple_key' => [
                'user' => 'user:bob',
                'relation' => 'writer',
                'object' => 'document:budget',
            ],
            'expectation' => false,
        ],
    ];
    
    $assertions = Assertions::fromArray($data);
    
    expect($assertions)->toBeInstanceOf(AssertionsInterface::class)
        ->and($assertions)->toHaveCount(2)
        ->and($assertions->offsetGet(0)->tupleKey->user)->toBe('user:anne')
        ->and($assertions->offsetGet(0)->expectation)->toBeTrue()
        ->and($assertions->offsetGet(1)->tupleKey->user)->toBe('user:bob')
        ->and($assertions->offsetGet(1)->expectation)->toBeFalse();
});