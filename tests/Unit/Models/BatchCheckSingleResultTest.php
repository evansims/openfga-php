<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Models;

use OpenFGA\Models\BatchCheckSingleResult;
use OpenFGA\Schemas\SchemaInterface;

it('creates a single result with allowed true', function (): void {
    $result = new BatchCheckSingleResult(allowed: true);

    expect($result->getAllowed())->toBeTrue();
    expect($result->getError())->toBeNull();
});

it('creates a single result with allowed false', function (): void {
    $result = new BatchCheckSingleResult(allowed: false);

    expect($result->getAllowed())->toBeFalse();
    expect($result->getError())->toBeNull();
});

it('creates a single result with allowed null', function (): void {
    $result = new BatchCheckSingleResult(allowed: null);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBeNull();
});

it('creates a single result with error', function (): void {
    $error = (object) ['code' => 'INTERNAL_ERROR', 'message' => 'Something went wrong'];
    $result = new BatchCheckSingleResult(allowed: null, error: $error);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBe($error);
});

it('creates a single result with both allowed and error', function (): void {
    $error = (object) ['code' => 'TIMEOUT', 'message' => 'Request timed out'];
    $result = new BatchCheckSingleResult(allowed: false, error: $error);

    expect($result->getAllowed())->toBeFalse();
    expect($result->getError())->toBe($error);
});

it('creates a single result with default values', function (): void {
    $result = new BatchCheckSingleResult;

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBeNull();
});

it('serializes to JSON correctly with allowed true', function (): void {
    $result = new BatchCheckSingleResult(allowed: true);
    $json = $result->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKey('allowed');
    expect($json['allowed'])->toBeTrue();
    expect($json)->not->toHaveKey('error');
});

it('serializes to JSON correctly with allowed false', function (): void {
    $result = new BatchCheckSingleResult(allowed: false);
    $json = $result->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKey('allowed');
    expect($json['allowed'])->toBeFalse();
    expect($json)->not->toHaveKey('error');
});

it('serializes to JSON correctly with error', function (): void {
    $error = (object) ['code' => 'INTERNAL_ERROR', 'message' => 'Something went wrong'];
    $result = new BatchCheckSingleResult(allowed: null, error: $error);
    $json = $result->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKey('error');
    expect($json['error'])->toBe($error);
    expect($json)->not->toHaveKey('allowed');
});

it('serializes to JSON correctly with both allowed and error', function (): void {
    $error = (object) ['code' => 'TIMEOUT', 'message' => 'Request timed out'];
    $result = new BatchCheckSingleResult(allowed: false, error: $error);
    $json = $result->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toHaveKeys(['allowed', 'error']);
    expect($json['allowed'])->toBeFalse();
    expect($json['error'])->toBe($error);
});

it('serializes to JSON correctly with null values (empty result)', function (): void {
    $result = new BatchCheckSingleResult;
    $json = $result->jsonSerialize();

    expect($json)->toBeArray();
    expect($json)->toBeEmpty(); // null values are filtered out
});

it('converts to array correctly with allowed true', function (): void {
    $result = new BatchCheckSingleResult(allowed: true);
    $array = $result->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKey('allowed');
    expect($array['allowed'])->toBeTrue();
});

it('converts to array correctly with error', function (): void {
    $error = (object) ['code' => 'INTERNAL_ERROR', 'message' => 'Something went wrong'];
    $result = new BatchCheckSingleResult(error: $error);
    $array = $result->toArray();

    expect($array)->toBeArray();
    expect($array)->toHaveKey('error');
    expect($array['error'])->toBe($error);
});

it('converts to array correctly with null values', function (): void {
    $result = new BatchCheckSingleResult;
    $array = $result->toArray();

    expect($array)->toBeArray();
    expect($array)->toBeEmpty();
});

it('creates from array with allowed true', function (): void {
    $data = ['allowed' => true];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeTrue();
    expect($result->getError())->toBeNull();
});

it('creates from array with allowed false', function (): void {
    $data = ['allowed' => false];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeFalse();
    expect($result->getError())->toBeNull();
});

it('creates from array with error', function (): void {
    $error = (object) ['code' => 'INTERNAL_ERROR', 'message' => 'Something went wrong'];
    $data = ['error' => $error];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBe($error);
});

it('creates from array with both allowed and error', function (): void {
    $error = (object) ['code' => 'TIMEOUT', 'message' => 'Request timed out'];
    $data = ['allowed' => false, 'error' => $error];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeFalse();
    expect($result->getError())->toBe($error);
});

it('creates from array with empty data', function (): void {
    $data = [];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBeNull();
});

it('ignores invalid allowed value when creating from array', function (): void {
    $data = ['allowed' => 'not-a-boolean'];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBeNull();
});

it('ignores invalid error value when creating from array', function (): void {
    $data = ['error' => 'not-an-object'];
    $result = BatchCheckSingleResult::fromArray($data);

    expect($result->getAllowed())->toBeNull();
    expect($result->getError())->toBeNull();
});

it('has valid schema', function (): void {
    $schema = BatchCheckSingleResult::schema();

    expect($schema)->toBeInstanceOf(SchemaInterface::class);
    expect($schema->getClassName())->toBe(BatchCheckSingleResult::class);
    expect($schema->getProperties())->toHaveCount(2);
});

it('round-trip serialization works correctly with allowed', function (): void {
    $original = new BatchCheckSingleResult(allowed: true);

    // Serialize to array and back
    $array = $original->toArray();
    $reconstructed = BatchCheckSingleResult::fromArray($array);

    expect($reconstructed->getAllowed())->toBe($original->getAllowed());
    expect($reconstructed->getError())->toBe($original->getError());
});

it('round-trip serialization works correctly with error', function (): void {
    $error = (object) ['code' => 'INTERNAL_ERROR', 'message' => 'Something went wrong'];
    $original = new BatchCheckSingleResult(error: $error);

    // Serialize to array and back
    $array = $original->toArray();
    $reconstructed = BatchCheckSingleResult::fromArray($array);

    expect($reconstructed->getAllowed())->toBe($original->getAllowed());
    expect($reconstructed->getError())->toBe($original->getError());
});

it('round-trip serialization works correctly with both values', function (): void {
    $error = (object) ['code' => 'TIMEOUT', 'message' => 'Request timed out'];
    $original = new BatchCheckSingleResult(allowed: false, error: $error);

    // Serialize to array and back
    $array = $original->toArray();
    $reconstructed = BatchCheckSingleResult::fromArray($array);

    expect($reconstructed->getAllowed())->toBe($original->getAllowed());
    expect($reconstructed->getError())->toBe($original->getError());
});
