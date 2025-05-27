<?php

declare(strict_types=1);

use OpenFGA\Models\{Tuple, TupleInterface, TupleKey};
use OpenFGA\Schema\SchemaInterface;

describe('Tuple Model', function (): void {
    test('implements TupleInterface', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable();

        $tuple = new Tuple(key: $key, timestamp: $timestamp);

        expect($tuple)->toBeInstanceOf(TupleInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable('2023-12-25 10:30:00');

        $tuple = new Tuple(key: $key, timestamp: $timestamp);

        expect($tuple->getKey())->toBe($key);
        expect($tuple->getTimestamp())->toBe($timestamp);
    });

    test('serializes to JSON with UTC timestamp', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable('2023-12-25 10:30:00', new DateTimeZone('America/New_York'));

        $tuple = new Tuple(key: $key, timestamp: $timestamp);
        $json = $tuple->jsonSerialize();

        expect($json)->toHaveKeys(['key', 'timestamp']);
        expect($json['key'])->toBe($key->jsonSerialize());
        // EST is UTC-5, so 10:30 EST becomes 15:30 UTC
        expect($json['timestamp'])->toBe('2023-12-25T15:30:00+00:00');
    });

    test('preserves UTC timestamps', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable('2023-12-25T10:30:00Z');

        $tuple = new Tuple(key: $key, timestamp: $timestamp);
        $json = $tuple->jsonSerialize();

        expect($json['timestamp'])->toBe('2023-12-25T10:30:00+00:00');
    });

    test('handles different timezone inputs', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );

        $timezones = [
            ['timezone' => 'Asia/Tokyo', 'input' => '2023-12-25 10:30:00', 'expected' => '2023-12-25T01:30:00+00:00'], // JST is UTC+9
            ['timezone' => 'Europe/London', 'input' => '2023-06-25 10:30:00', 'expected' => '2023-06-25T09:30:00+00:00'], // BST is UTC+1
            ['timezone' => 'Pacific/Honolulu', 'input' => '2023-12-25 10:30:00', 'expected' => '2023-12-25T20:30:00+00:00'], // HST is UTC-10
        ];

        foreach ($timezones as $test) {
            $timestamp = new DateTimeImmutable($test['input'], new DateTimeZone($test['timezone']));
            $tuple = new Tuple(key: $key, timestamp: $timestamp);
            $json = $tuple->jsonSerialize();

            expect($json['timestamp'])->toBe($test['expected']);
        }
    });

    test('returns schema instance', function (): void {
        $schema = Tuple::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(Tuple::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        $keyProp = $properties[array_keys($properties)[0]];
        expect($keyProp->name)->toBe('key');
        expect($keyProp->type)->toBe(TupleKey::class);
        expect($keyProp->required)->toBe(true);

        $timestampProp = $properties[array_keys($properties)[1]];
        expect($timestampProp->name)->toBe('timestamp');
        expect($timestampProp->type)->toBe('string');
        expect($timestampProp->format)->toBe('datetime');
        expect($timestampProp->required)->toBe(true);
    });

    test('schema is cached', function (): void {
        $schema1 = Tuple::schema();
        $schema2 = Tuple::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles microseconds in timestamp', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable('2023-12-25 10:30:00.123456', new DateTimeZone('UTC'));

        $tuple = new Tuple(key: $key, timestamp: $timestamp);
        $json = $tuple->jsonSerialize();

        // RFC3339 format doesn't include microseconds
        expect($json['timestamp'])->toBe('2023-12-25T10:30:00+00:00');
    });

    test('handles current time', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
        );
        $timestamp = new DateTimeImmutable('now');

        $tuple = new Tuple(key: $key, timestamp: $timestamp);
        $json = $tuple->jsonSerialize();

        expect($json['timestamp'])->toMatch('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\+00:00$/');
    });

    test('tuple key with condition is preserved', function (): void {
        $key = new TupleKey(
            user: 'user:anne',
            relation: 'viewer',
            object: 'document:roadmap',
            condition: new OpenFGA\Models\Condition(
                name: 'inRegion',
                context: new OpenFGA\Models\Collections\ConditionParameters([
                    new OpenFGA\Models\ConditionParameter(name: 'region', value: 'us-east'),
                ]),
            ),
        );
        $timestamp = new DateTimeImmutable();

        $tuple = new Tuple(key: $key, timestamp: $timestamp);

        expect($tuple->getKey())->toBe($key);
        expect($tuple->getKey()->getCondition())->not->toBeNull();
    });
});
