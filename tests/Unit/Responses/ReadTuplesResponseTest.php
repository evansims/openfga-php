<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Responses;

use DateTimeImmutable;
use OpenFGA\Exceptions\NetworkException;
use OpenFGA\Models\Collections\Tuples;
use OpenFGA\Models\ConditionInterface;
use OpenFGA\Models\{Tuple, TupleKey};
use OpenFGA\Responses\{ReadTuplesResponse, ReadTuplesResponseInterface};
use OpenFGA\Schema\{SchemaInterface, SchemaValidator};
use OpenFGA\Tests\Support\Responses\SimpleResponse;
use Psr\Http\Message\RequestInterface;

describe('ReadTuplesResponse', function (): void {
    test('implements ReadTuplesResponseInterface', function (): void {
        $tuples = new Tuples;
        $response = new ReadTuplesResponse($tuples);
        expect($response)->toBeInstanceOf(ReadTuplesResponseInterface::class);
    });

    test('constructs with tuples only', function (): void {
        $tuple1 = new Tuple(
            key: new TupleKey('user:anne', 'viewer', 'document:budget.pdf'),
            timestamp: new DateTimeImmutable('2024-01-01 10:00:00'),
        );

        $tuple2 = new Tuple(
            key: new TupleKey('user:bob', 'editor', 'document:budget.pdf'),
            timestamp: new DateTimeImmutable('2024-01-01 11:00:00'),
        );

        $tuples = new Tuples($tuple1, $tuple2);

        $response = new ReadTuplesResponse($tuples);

        expect($response->getTuples())->toBe($tuples);
        expect($response->getContinuationToken())->toBeNull();
    });

    test('constructs with tuples and continuation token', function (): void {
        $tuples = new Tuples;
        $continuationToken = 'next-page-token-def';

        $response = new ReadTuplesResponse($tuples, $continuationToken);

        expect($response->getTuples())->toBe($tuples);
        expect($response->getContinuationToken())->toBe($continuationToken);
    });

    test('handles empty tuples collection', function (): void {
        $tuples = new Tuples;
        $response = new ReadTuplesResponse($tuples);

        expect($response->getTuples())->toBe($tuples);
        expect($response->getTuples()->count())->toBe(0);
    });

    test('handles large tuples collection', function (): void {
        $tuples = new Tuples;

        for ($i = 1; 50 >= $i; ++$i) {
            $tuple = new Tuple(
                key: new TupleKey("user:user{$i}", 'viewer', "document:doc{$i}.pdf"),
                timestamp: new DateTimeImmutable("2024-01-01 10:{$i}:00"),
            );

            $tuples->add($tuple);
        }

        $response = new ReadTuplesResponse($tuples, 'large-set-token');

        expect($response->getTuples()->count())->toBe(50);
        expect($response->getContinuationToken())->toBe('large-set-token');
    });

    test('handles tuples with conditions', function (): void {
        $condition = test()->createMock(ConditionInterface::class);

        $tupleKey = new TupleKey(
            user: 'user:charlie',
            relation: 'can_approve',
            object: 'expense:123',
            condition: $condition,
        );

        $tuple = new Tuple(
            key: $tupleKey,
            timestamp: new DateTimeImmutable('2024-01-01 12:00:00'),
        );

        $tuples = new Tuples($tuple);
        $response = new ReadTuplesResponse($tuples);

        expect($response->getTuples()->count())->toBe(1);

        $tupleArray = [];
        foreach ($response->getTuples() as $t) {
            $tupleArray[] = $t;
        }

        expect($tupleArray[0]->getKey()->getCondition())->toBe($condition);
    });

    test('schema returns expected structure', function (): void {
        $schema = ReadTuplesResponse::schema();

        expect($schema)->toBeInstanceOf(SchemaInterface::class);
        expect($schema->getClassName())->toBe(ReadTuplesResponse::class);

        $properties = $schema->getProperties();
        expect($properties)->toHaveCount(2);

        expect($properties)->toHaveKeys(['tuples', 'continuation_token']);

        expect($properties['tuples']->name)->toBe('tuples');
        expect($properties['tuples']->type)->toBe('object');
        expect($properties['tuples']->required)->toBeTrue();

        expect($properties['continuation_token']->name)->toBe('continuation_token');
        expect($properties['continuation_token']->type)->toBe('string');
        expect($properties['continuation_token']->required)->toBeFalse();
    });

    test('schema is cached', function (): void {
        $schema1 = ReadTuplesResponse::schema();
        $schema2 = ReadTuplesResponse::schema();

        expect($schema1)->toBe($schema2);
    });

    test('handles empty continuation token', function (): void {
        $tuples = new Tuples;
        $response = new ReadTuplesResponse($tuples, '');

        expect($response->getContinuationToken())->toBe('');
    });

    test('handles tuples with different timestamps', function (): void {
        $tuple1 = new Tuple(
            key: new TupleKey('user:diana', 'admin', 'system:core'),
            timestamp: new DateTimeImmutable('2024-01-01 10:00:00.123456'),
        );

        $tuple2 = new Tuple(
            key: new TupleKey('user:edward', 'viewer', 'document:readme.txt'),
            timestamp: new DateTimeImmutable('2024-01-01 10:00:00.654321'),
        );

        $tuples = new Tuples($tuple1, $tuple2);
        $response = new ReadTuplesResponse($tuples);

        expect($response->getTuples()->count())->toBe(2);

        $tupleArray = [];
        foreach ($response->getTuples() as $t) {
            $tupleArray[] = $t;
        }

        expect($tupleArray[0]->getTimestamp()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.123456');
        expect($tupleArray[1]->getTimestamp()->format('Y-m-d H:i:s.u'))->toBe('2024-01-01 10:00:00.654321');
    });

    test('fromResponse handles error responses with non-200 status', function (): void {
        $httpResponse = new SimpleResponse(400, json_encode(['code' => 'invalid_request', 'message' => 'Bad request']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadTuplesResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 401 unauthorized error', function (): void {
        $httpResponse = new SimpleResponse(401, json_encode(['code' => 'unauthenticated', 'message' => 'Unauthorized']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadTuplesResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);

    test('fromResponse handles 500 internal server error', function (): void {
        $httpResponse = new SimpleResponse(500, json_encode(['code' => 'internal_error', 'message' => 'Internal server error']));
        $request = test()->createMock(RequestInterface::class);
        $validator = new SchemaValidator;

        ReadTuplesResponse::fromResponse($httpResponse, $request, $validator);
    })->throws(NetworkException::class);
});
