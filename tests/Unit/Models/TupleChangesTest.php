<?php

declare(strict_types=1);

use OpenFGA\Models\{TupleChange, TupleChanges, TupleKey, TupleOperation};

test('empty collection', function (): void {
    $tupleChanges = new TupleChanges();

    expect($tupleChanges)->toHaveCount(0);
});

test('add tuple change', function (): void {
    $tupleChanges = new TupleChanges();
    $tupleKey = new TupleKey('document:1', 'reader', 'user:1');
    $change = new TupleChange(
        $tupleKey,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable(),
    );

    $tupleChanges[] = $change;

    expect($tupleChanges)->toHaveCount(1)
        ->and($tupleChanges[0])->toBe($change);
});

test('create with changes', function (): void {
    $tupleKey1 = new TupleKey('document:1', 'reader', 'user:1');
    $change1 = new TupleChange(
        $tupleKey1,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable(),
    );

    $tupleKey2 = new TupleKey('document:2', 'writer', 'user:2');
    $change2 = new TupleChange(
        $tupleKey2,
        TupleOperation::TUPLE_OPERATION_DELETE,
        new DateTimeImmutable(),
    );

    $tupleChanges = new TupleChanges([$change1, $change2]);

    expect($tupleChanges)->toHaveCount(2)
        ->and($tupleChanges[0])->toBe($change1)
        ->and($tupleChanges[1])->toBe($change2);
});

test('json serialize', function (): void {
    $tupleKey1 = new TupleKey('document:1', 'reader', 'user:1');
    $change1 = new TupleChange(
        $tupleKey1,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable('2023-01-01T00:00:00+00:00'),
    );

    $tupleKey2 = new TupleKey('document:2', 'writer', 'user:2');
    $change2 = new TupleChange(
        $tupleKey2,
        TupleOperation::TUPLE_OPERATION_DELETE,
        new DateTimeImmutable('2023-01-02T00:00:00+00:00'),
    );

    $tupleChanges = new TupleChanges([$change1, $change2]);

    $result = $tupleChanges->jsonSerialize();

    expect($result)->toBe([
        $change1->jsonSerialize(),
        $change2->jsonSerialize(),
    ]);
});

test('filter changes by operation', function (): void {
    $tupleKey1 = new TupleKey('document:1', 'reader', 'user:1');
    $change1 = new TupleChange(
        $tupleKey1,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable(),
    );

    $tupleKey2 = new TupleKey('document:2', 'writer', 'user:2');
    $change2 = new TupleChange(
        $tupleKey2,
        TupleOperation::TUPLE_OPERATION_DELETE,
        new DateTimeImmutable(),
    );

    $tupleChanges = new TupleChanges([$change1, $change2]);

    $writeChanges = $tupleChanges->filter(
        fn (TupleChange $change) => TupleOperation::TUPLE_OPERATION_WRITE === $change->getOperation(),
    );

    expect($writeChanges)->toHaveCount(1)
        ->and($writeChanges[0])->toBe($change1);
});

test('can find tuple change by key', function (): void {
    $tupleKey = new TupleKey(
        user: 'user:81684243-9356-4421-8a0e-99c9646704ce',
        relation: 'viewer',
        object: 'document:roadmap',
    );

    $tupleChanges = new TupleChanges([
        new TupleChange(
            tupleKey: $tupleKey,
            operation: TupleOperation::TUPLE_OPERATION_WRITE,
            timestamp: new DateTimeImmutable(),
        ),
    ]);

    $found = $tupleChanges->first(function (TupleChange $tupleChange) use ($tupleKey) {
        $key = $tupleChange->getTupleKey();

        return $key->getUser() === $tupleKey->getUser()
            && $key->getRelation() === $tupleKey->getRelation()
            && $key->getObject() === $tupleKey->getObject();
    });

    expect($found)->not->toBeNull()
        ->and($found->getTupleKey()->getUser())->toBe('user:81684243-9356-4421-8a0e-99c9646704ce')
        ->and($found->getTupleKey()->getRelation())->toBe('viewer')
        ->and($found->getTupleKey()->getObject())->toBe('document:roadmap');
});

test('find change by tuple key', function (): void {
    $tupleKey1 = new TupleKey('user:1', 'reader', 'document:1');
    $change1 = new TupleChange(
        $tupleKey1,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable(),
    );

    $tupleKey2 = new TupleKey('user:2', 'writer', 'document:2');
    $change2 = new TupleChange(
        $tupleKey2,
        TupleOperation::TUPLE_OPERATION_DELETE,
        new DateTimeImmutable(),
    );

    $tupleChanges = new TupleChanges([$change1, $change2]);

    $found = $tupleChanges->first(
        function (TupleChange $change) {
            $object = $change->getTupleKey()->getObject();
            $relation = $change->getTupleKey()->getRelation();
            $user = $change->getTupleKey()->getUser();

            return 'document:2' === $object
                   && 'writer' === $relation
                   && 'user:2' === $user;
        },
    );

    expect($found)->not->toBeNull()
        ->and($found->getTupleKey()->getObject())->toBe('document:2')
        ->and($found->getTupleKey()->getRelation())->toBe('writer')
        ->and($found->getTupleKey()->getUser())->toBe('user:2')
        ->and($found->getOperation())->toBe(TupleOperation::TUPLE_OPERATION_DELETE);
});

test('convert to array', function (): void {
    $tupleKey1 = new TupleKey('document:1', 'reader', 'user:1');
    $change1 = new TupleChange(
        $tupleKey1,
        TupleOperation::TUPLE_OPERATION_WRITE,
        new DateTimeImmutable(),
    );

    $tupleKey2 = new TupleKey('document:2', 'writer', 'user:2');
    $change2 = new TupleChange(
        $tupleKey2,
        TupleOperation::TUPLE_OPERATION_DELETE,
        new DateTimeImmutable(),
    );

    $tupleChanges = new TupleChanges([$change1, $change2]);

    $array = $tupleChanges->toArray();

    expect($array)->toBe([$change1, $change2]);
});
