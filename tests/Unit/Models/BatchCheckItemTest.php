<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use Exception;
use InvalidArgumentException;
use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\{BatchCheckItem};
use OpenFGA\Schema\SchemaInterface;

use function OpenFGA\{tuple, tuples};

it('creates a batch check item with required parameters', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id-1',
    );

    expect($item->getTupleKey())->toBe($tupleKey);
    expect($item->getCorrelationId())->toBe('test-correlation-id-1');
    expect($item->getContextualTuples())->toBeNull();
    expect($item->getContext())->toBeNull();
});

it('creates a batch check item with all parameters', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $contextualTuples = tuples(
        tuple('user:bob', 'writer', 'document:budget'),
    );
    $context = (object) ['department' => 'engineering'];

    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id-2',
        contextualTuples: $contextualTuples,
        context: $context,
    );

    expect($item->getTupleKey())->toBe($tupleKey);
    expect($item->getCorrelationId())->toBe('test-correlation-id-2');
    expect($item->getContextualTuples())->toBe($contextualTuples);
    expect($item->getContext())->toBe($context);
});

it('validates correlation ID format - valid formats', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');

    // Valid correlation IDs
    $validIds = [
        'test-id-1',
        'ABC123',
        'user-123-check',
        'a',
        '123456789012345678901234567890123456', // 36 chars max
        'test_id_with_underscores',
        'UPPERCASE-ID',
        'lowercase-id',
        'mixed-Case-ID-123',
    ];

    foreach ($validIds as $correlationId) {
        expect(fn () => new BatchCheckItem(
            tupleKey: $tupleKey,
            correlationId: $correlationId,
        ))->not->toThrow(Exception::class);
    }
});

it('validates correlation ID format - invalid formats', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');

    // Invalid correlation IDs
    $invalidIds = [
        '', // empty
        'id with spaces',
        'id@with!special#chars',
        'id.with.dots',
        'id/with/slashes',
        'id:with:colons',
        'id+with+plus',
        'id=with=equals',
        '1234567890123456789012345678901234567', // 37 chars - too long
        'id(with)parentheses',
        'id[with]brackets',
        'id{with}braces',
        'id<with>angles',
        'id|with|pipes',
        'id\\with\\backslashes',
        'id"with"quotes',
        "id'with'quotes",
        'id,with,commas',
        'id;with;semicolons',
        'id%with%percent',
        'id^with^caret',
        'id&with&ampersand',
        'id*with*asterisk',
        'id?with?question',
    ];

    foreach ($invalidIds as $correlationId) {
        expect(fn () => new BatchCheckItem(
            tupleKey: $tupleKey,
            correlationId: $correlationId,
        ))->toThrow(ClientException::class);
    }
});

it('serializes to JSON correctly with minimal data', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id',
    );

    $json = $item->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKeys(['tuple_key', 'correlation_id']);
    expect($json)->not->toHaveKey('contextual_tuples');
    expect($json)->not->toHaveKey('context');
    expect($json['correlation_id'])->toBe('test-correlation-id');
    expect($json['tuple_key'])->toBeArray();
});

it('serializes to JSON correctly with all data', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $contextualTuples = tuples(
        tuple('user:bob', 'writer', 'document:budget'),
    );
    $context = (object) ['department' => 'engineering'];

    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id',
        contextualTuples: $contextualTuples,
        context: $context,
    );

    $json = $item->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKeys(['tuple_key', 'correlation_id', 'contextual_tuples', 'context']);
    expect($json['correlation_id'])->toBe('test-correlation-id');
    expect($json['tuple_key'])->toBeArray();
    expect($json['contextual_tuples'])->toBeArray();
    expect($json['context'])->toBe($context);
});

it('converts to array correctly', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $contextualTuples = tuples(
        tuple('user:bob', 'writer', 'document:budget'),
    );
    $context = (object) ['department' => 'engineering'];

    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id',
        contextualTuples: $contextualTuples,
        context: $context,
    );

    $array = $item->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKeys(['tuple_key', 'correlation_id', 'contextual_tuples', 'context']);
    expect($array['correlation_id'])->toBe('test-correlation-id');
    expect($array['tuple_key'])->toBeArray();
    expect($array['contextual_tuples'])->toBeArray();
    expect($array['context'])->toBe($context);
});

it('creates from array with minimal data', function (): void {
    $data = [
        'tuple_key' => [
            'user' => 'user:alice',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
        'correlation_id' => 'test-correlation-id',
    ];

    $item = BatchCheckItem::fromArray($data);

    expect($item->getCorrelationId())->toBe('test-correlation-id');
    expect($item->getTupleKey()->getUser())->toBe('user:alice');
    expect($item->getTupleKey()->getRelation())->toBe('reader');
    expect($item->getTupleKey()->getObject())->toBe('document:budget');
    expect($item->getContextualTuples())->toBeNull();
    expect($item->getContext())->toBeNull();
});

it('creates from array with all data', function (): void {
    $context = (object) ['department' => 'engineering'];
    $data = [
        'tuple_key' => [
            'user' => 'user:alice',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
        'correlation_id' => 'test-correlation-id',
        'contextual_tuples' => [
            [
                'user' => 'user:bob',
                'relation' => 'writer',
                'object' => 'document:budget',
            ],
        ],
        'context' => $context,
    ];

    $item = BatchCheckItem::fromArray($data);

    expect($item->getCorrelationId())->toBe('test-correlation-id');
    expect($item->getTupleKey()->getUser())->toBe('user:alice');
    expect($item->getContextualTuples())->not->toBeNull();
    expect($item->getContextualTuples()->count())->toBe(1);
    expect($item->getContext())->toBe($context);
});

it('throws exception when creating from array with missing tuple_key', function (): void {
    $data = [
        'correlation_id' => 'test-correlation-id',
    ];

    expect(fn () => BatchCheckItem::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'Missing or invalid tuple_key data');
});

it('throws exception when creating from array with invalid tuple_key', function (): void {
    $data = [
        'tuple_key' => 'not-an-array',
        'correlation_id' => 'test-correlation-id',
    ];

    expect(fn () => BatchCheckItem::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'Missing or invalid tuple_key data');
});

it('throws exception when creating from array with missing correlation_id', function (): void {
    $data = [
        'tuple_key' => [
            'user' => 'user:alice',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
    ];

    expect(fn () => BatchCheckItem::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'Missing or invalid correlation_id');
});

it('throws exception when creating from array with invalid tuple key structure', function (): void {
    $data = [
        'tuple_key' => [
            'user' => 'user:alice',
            'relation' => 'reader',
            // missing object
        ],
        'correlation_id' => 'test-correlation-id',
    ];

    expect(fn () => BatchCheckItem::fromArray($data))
        ->toThrow(InvalidArgumentException::class, 'Invalid tuple key data structure');
});

it('handles invalid contextual tuples gracefully when creating from array', function (): void {
    $data = [
        'tuple_key' => [
            'user' => 'user:alice',
            'relation' => 'reader',
            'object' => 'document:budget',
        ],
        'correlation_id' => 'test-correlation-id',
        'contextual_tuples' => [
            [
                'user' => 'user:bob',
                // missing relation and object - should be ignored
            ],
            [
                'user' => 'user:charlie',
                'relation' => 'writer',
                'object' => 'document:spec',
            ],
        ],
    ];

    $item = BatchCheckItem::fromArray($data);

    expect($item->getContextualTuples())->not->toBeNull();
    expect($item->getContextualTuples()->count())->toBe(1); // Only valid tuple added
});

it('has valid schema', function (): void {
    $schema = BatchCheckItem::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(BatchCheckItem::class);
    expect($schema->getProperties())->toHaveCount(4);
});

it('round-trip serialization works correctly', function (): void {
    $tupleKey = tuple('user:alice', 'reader', 'document:budget');
    $contextualTuples = tuples(
        tuple('user:bob', 'writer', 'document:budget'),
    );
    $context = (object) ['department' => 'engineering'];

    $original = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id',
        contextualTuples: $contextualTuples,
        context: $context,
    );

    // Serialize to array and back
    $array = $original->jsonSerialize();
    $reconstructed = BatchCheckItem::fromArray($array);

    expect($reconstructed->getCorrelationId())->toBe($original->getCorrelationId());
    expect($reconstructed->getTupleKey()->getUser())->toBe($original->getTupleKey()->getUser());
    expect($reconstructed->getTupleKey()->getRelation())->toBe($original->getTupleKey()->getRelation());
    expect($reconstructed->getTupleKey()->getObject())->toBe($original->getTupleKey()->getObject());
    expect($reconstructed->getContextualTuples())->not->toBeNull();
    expect($reconstructed->getContextualTuples()->count())->toBe($original->getContextualTuples()->count());
    expect($reconstructed->getContext())->toBe($original->getContext());
});
