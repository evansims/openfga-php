<?php

declare(strict_types=1);

use OpenFGA\Models\{Tuple, TupleKey};

test('constructor and getters', function (): void {
    $key = new TupleKey('document:1', 'reader', 'user:1');
    $timestamp = new DateTimeImmutable('2023-01-01T12:00:00+00:00');

    $tuple = new Tuple($key, $timestamp);

    expect($tuple->getKey())->toBe($key)
        ->and($tuple->getTimestamp())->toEqual($timestamp);
});

test('json serialize', function (): void {
    $key = new TupleKey('document:1', 'reader', 'user:1');
    $timestamp = new DateTimeImmutable('2023-01-01T12:00:00+00:00');

    $tuple = new Tuple($key, $timestamp);

    $result = $tuple->jsonSerialize();

    expect($result)->toBe([
        'key' => $key->jsonSerialize(),
        'timestamp' => '2023-01-01T12:00:00+00:00',
    ]);
});

test('json serialize with current timestamp', function (): void {
    $key = new TupleKey('document:1', 'reader', 'user:1');
    $timestamp = new DateTimeImmutable();

    $tuple = new Tuple($key, $timestamp);

    $result = $tuple->jsonSerialize();

    expect($result)->toHaveKeys(['key', 'timestamp'])
        ->and($result['key'])->toBe($key->jsonSerialize())
        ->and(DateTimeImmutable::createFromFormat(DATE_ATOM, $result['timestamp']))->toBeInstanceOf(DateTimeImmutable::class);
});

test('schema', function (): void {
    $schema = Tuple::schema();

    expect($schema)
        ->getClassName()->toBe(Tuple::class)
        ->getProperties()->toHaveCount(2)
        ->and($schema->getProperty('key'))->toMatchObject([
            'name' => 'key',
            'type' => 'OpenFGA\\Models\\TupleKey',
            'required' => true,
        ])
        ->and($schema->getProperty('timestamp'))->toMatchObject([
            'name' => 'timestamp',
            'type' => 'string',
            'format' => 'date-time',
            'required' => true,
        ]);
});
