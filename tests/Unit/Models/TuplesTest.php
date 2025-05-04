<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\{AssertionTupleKey, Condition, ContextualTupleKey, ContextualTupleKeys, TupleKey, TupleKeys};

it('can create a TupleKey instance with constructor', function () {
    $tupleKey = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    expect($tupleKey->user)->toBe('user:anne')
        ->and($tupleKey->relation)->toBe('reader')
        ->and($tupleKey->object)->toBe('document:budget')
        ->and($tupleKey->condition)->toBeNull();
});

it('can create a TupleKey instance with condition', function () {
    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= 18'
    );

    $tupleKey = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: $condition
    );

    expect($tupleKey->user)->toBe('user:anne')
        ->and($tupleKey->relation)->toBe('reader')
        ->and($tupleKey->object)->toBe('document:budget')
        ->and($tupleKey->condition)->toBe($condition);
});

it('can convert TupleKey to array', function () {
    $tupleKey = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    $array = $tupleKey->toArray();

    expect($array)->toBeArray()
        ->and($array['user'])->toBe('user:anne')
        ->and($array['relation'])->toBe('reader')
        ->and($array['object'])->toBe('document:budget')
        ->and($array['condition'])->toBeNull();
});

it('can convert TupleKey with condition to array', function () {
    $condition = new Condition(
        name: 'test_condition',
        expression: 'subject.age >= 18'
    );

    $tupleKey = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: $condition
    );

    $array = $tupleKey->toArray();

    expect($array)->toBeArray()
        ->and($array['user'])->toBe('user:anne')
        ->and($array['relation'])->toBe('reader')
        ->and($array['object'])->toBe('document:budget')
        ->and($array['condition'])->toBe($condition->toArray());
});

it('can create TupleKey from array', function () {
    $data = [
        'user' => 'user:anne',
        'relation' => 'reader',
        'object' => 'document:budget',
    ];

    $tupleKey = TupleKey::fromArray($data);

    expect($tupleKey->user)->toBe('user:anne')
        ->and($tupleKey->relation)->toBe('reader')
        ->and($tupleKey->object)->toBe('document:budget')
        ->and($tupleKey->condition)->toBeNull();
});

it('can create TupleKey with condition from array', function () {
    $data = [
        'user' => 'user:anne',
        'relation' => 'reader',
        'object' => 'document:budget',
        'condition' => [
            'name' => 'test_condition',
            'expression' => 'subject.age >= 18',
        ],
    ];

    $tupleKey = TupleKey::fromArray($data);

    expect($tupleKey->user)->toBe('user:anne')
        ->and($tupleKey->relation)->toBe('reader')
        ->and($tupleKey->object)->toBe('document:budget')
        ->and($tupleKey->condition)->toBeInstanceOf(Condition::class)
        ->and($tupleKey->condition->name)->toBe('test_condition')
        ->and($tupleKey->condition->expression)->toBe('subject.age >= 18');
});

it('can create and use a TupleKeys collection', function () {
    $tupleKeys = new TupleKeys();

    $tupleKey1 = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    $tupleKey2 = new TupleKey(
        user: 'user:bob',
        relation: 'writer',
        object: 'document:budget'
    );

    $tupleKeys->add($tupleKey1);
    $tupleKeys->add($tupleKey2);

    expect($tupleKeys)->toHaveCount(2)
        ->and($tupleKeys[0])->toBe($tupleKey1)
        ->and($tupleKeys[1])->toBe($tupleKey2);
});

it('can get current TupleKey from TupleKeys collection', function () {
    $tupleKeys = new TupleKeys();

    $tupleKey = new TupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    $tupleKeys->add($tupleKey);

    expect($tupleKeys->current())->toBe($tupleKey);
});

it('can create TupleKeys collection from array', function () {
    $data = [
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
    ];

    $tupleKeys = TupleKeys::fromArray($data);

    expect($tupleKeys)->toHaveCount(2)
        ->and($tupleKeys[0]->user)->toBe('user:anne')
        ->and($tupleKeys[0]->relation)->toBe('reader')
        ->and($tupleKeys[0]->object)->toBe('document:budget')
        ->and($tupleKeys[1]->user)->toBe('user:bob')
        ->and($tupleKeys[1]->relation)->toBe('writer')
        ->and($tupleKeys[1]->object)->toBe('document:budget');
});

it('can create a ContextualTupleKey instance with constructor', function () {
    $contextualTupleKey = new ContextualTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: 'test_condition'
    );

    expect($contextualTupleKey->user)->toBe('user:anne')
        ->and($contextualTupleKey->relation)->toBe('reader')
        ->and($contextualTupleKey->object)->toBe('document:budget')
        ->and($contextualTupleKey->condition)->toBe('test_condition');
});

it('can convert ContextualTupleKey to array', function () {
    $contextualTupleKey = new ContextualTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: 'test_condition'
    );

    $array = $contextualTupleKey->toArray();

    expect($array)->toBeArray()
        ->and($array['user'])->toBe('user:anne')
        ->and($array['relation'])->toBe('reader')
        ->and($array['object'])->toBe('document:budget')
        ->and($array['condition'])->toBe('test_condition');
});

it('can create ContextualTupleKey from array', function () {
    $data = [
        'user' => 'user:anne',
        'relation' => 'reader',
        'object' => 'document:budget',
        'condition' => 'test_condition',
    ];

    $contextualTupleKey = ContextualTupleKey::fromArray($data);

    expect($contextualTupleKey->user)->toBe('user:anne')
        ->and($contextualTupleKey->relation)->toBe('reader')
        ->and($contextualTupleKey->object)->toBe('document:budget')
        ->and($contextualTupleKey->condition)->toBe('test_condition');
});

it('can create and use a ContextualTupleKeys collection', function () {
    $contextualTupleKeys = new ContextualTupleKeys();

    $contextualTupleKey1 = new ContextualTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: 'test_condition1'
    );

    $contextualTupleKey2 = new ContextualTupleKey(
        user: 'user:bob',
        relation: 'writer',
        object: 'document:budget',
        condition: 'test_condition2'
    );

    $contextualTupleKeys->add($contextualTupleKey1);
    $contextualTupleKeys->add($contextualTupleKey2);

    expect($contextualTupleKeys)->toHaveCount(2)
        ->and($contextualTupleKeys[0])->toBe($contextualTupleKey1)
        ->and($contextualTupleKeys[1])->toBe($contextualTupleKey2);
});

it('can get current ContextualTupleKey from ContextualTupleKeys collection', function () {
    $contextualTupleKeys = new ContextualTupleKeys();

    $contextualTupleKey = new ContextualTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget',
        condition: 'test_condition'
    );

    $contextualTupleKeys->add($contextualTupleKey);

    expect($contextualTupleKeys->current())->toBe($contextualTupleKey);
});

it('can create ContextualTupleKeys collection from array', function () {
    $data = [
        [
            'user' => 'user:anne',
            'relation' => 'reader',
            'object' => 'document:budget',
            'condition' => 'test_condition1',
        ],
        [
            'user' => 'user:bob',
            'relation' => 'writer',
            'object' => 'document:budget',
            'condition' => 'test_condition2',
        ],
    ];

    $contextualTupleKeys = ContextualTupleKeys::fromArray($data);

    expect($contextualTupleKeys)->toHaveCount(2)
        ->and($contextualTupleKeys[0]->user)->toBe('user:anne')
        ->and($contextualTupleKeys[0]->relation)->toBe('reader')
        ->and($contextualTupleKeys[0]->object)->toBe('document:budget')
        ->and($contextualTupleKeys[0]->condition)->toBe('test_condition1')
        ->and($contextualTupleKeys[1]->user)->toBe('user:bob')
        ->and($contextualTupleKeys[1]->relation)->toBe('writer')
        ->and($contextualTupleKeys[1]->object)->toBe('document:budget')
        ->and($contextualTupleKeys[1]->condition)->toBe('test_condition2');
});

it('can create an AssertionTupleKey instance with constructor', function () {
    $assertionTupleKey = new AssertionTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    expect($assertionTupleKey->user)->toBe('user:anne')
        ->and($assertionTupleKey->relation)->toBe('reader')
        ->and($assertionTupleKey->object)->toBe('document:budget');
});

it('can convert AssertionTupleKey to array', function () {
    $assertionTupleKey = new AssertionTupleKey(
        user: 'user:anne',
        relation: 'reader',
        object: 'document:budget'
    );

    $array = $assertionTupleKey->toArray();

    expect($array)->toBeArray()
        ->and($array['user'])->toBe('user:anne')
        ->and($array['relation'])->toBe('reader')
        ->and($array['object'])->toBe('document:budget');
});

it('can create AssertionTupleKey from array', function () {
    $data = [
        'user' => 'user:anne',
        'relation' => 'reader',
        'object' => 'document:budget',
    ];

    $assertionTupleKey = AssertionTupleKey::fromArray($data);

    expect($assertionTupleKey->user)->toBe('user:anne')
        ->and($assertionTupleKey->relation)->toBe('reader')
        ->and($assertionTupleKey->object)->toBe('document:budget');
});
