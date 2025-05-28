<?php

declare(strict_types=1);

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Results\{Failure, Result, Success};

test('Result unwrap returns value for Success', function (): void {
    $value = 'test-value';
    $success = new Success($value);

    expect($success->unwrap())->toBe($value);
});

test('Result unwrap returns value for Success with callback', function (): void {
    $value = 'test-value';
    $success = new Success($value);

    $result = $success->unwrap(fn ($v) => strtoupper($v));
    expect($result)->toBe('TEST-VALUE');
});

test('Result unwrap throws for Failure with no callback', function (): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    expect(fn () => $failure->unwrap())
        ->toThrow($error::class);
});

test('Result unwrap returns callback result for Failure', function (): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    $result = $failure->unwrap(fn ($e) => 'handled: ' . $e->getMessage());
    expect($result)->toStartWith('handled: ');
});

test('Result unwrap with various callback return types for Failure', function (mixed $returnValue): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    $result = $failure->unwrap(fn () => $returnValue);
    expect($result)->toBe($returnValue);
})->with([
    'string return' => ['default-string'],
    'number return' => [42],
    'array return' => [['a', 'b', 'c']],
    'object return' => [(object) ['key' => 'value']],
    'boolean true return' => [true],
    'boolean false return' => [false],
    'null return' => [null],
]);

test('Result unwrap with falsy callback returns for Failure', function (): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    // Test various falsy return values
    expect($failure->unwrap(fn () => false))->toBeFalse();
    expect($failure->unwrap(fn () => 0))->toBe(0);
    expect($failure->unwrap(fn () => ''))->toBe('');
    expect($failure->unwrap(fn () => []))->toBe([]);
    expect($failure->unwrap(fn () => null))->toBeNull();
});

test('Result unwrap preserves Success value type', function (): void {
    $values = [
        'string' => 'test',
        'integer' => 42,
        'float' => 3.14,
        'boolean' => true,
        'array' => ['a', 'b'],
        'object' => (object) ['test' => true],
        'null' => null,
    ];

    foreach ($values as $value) {
        $success = new Success($value);
        expect($success->unwrap())->toBe($value);
        // Test with callback that doesn't transform
        expect($success->unwrap(fn ($v) => $v))->toBe($value);
    }
});

test('Result abstract class behavior through concrete implementations', function (): void {
    $value = 'test-value';
    $error = ClientError::Validation->exception();

    $success = new Success($value);
    $failure = new Failure($error);

    // Both should be instances of Result
    expect($success)->toBeInstanceOf(Result::class);
    expect($failure)->toBeInstanceOf(Result::class);

    // Test polymorphic behavior
    $results = [$success, $failure];

    foreach ($results as $result) {
        expect($result)->toBeInstanceOf(Result::class);
        // unwrap should work on both
        if ($result instanceof Success) {
            expect($result->unwrap())->toBe($value);
        } else {
            expect($result->unwrap(fn () => 'default'))->toBe('default');
        }
    }
});

test('Result unwrap method is inherited correctly', function (): void {
    $success = new Success('success-value');
    $failure = new Failure(ClientError::Validation->exception());

    // Verify the unwrap method exists and works as expected
    expect(method_exists($success, 'unwrap'))->toBeTrue();
    expect(method_exists($failure, 'unwrap'))->toBeTrue();

    // Verify it calls the correct internal methods
    expect($success->unwrap())->toBe('success-value');
    expect($failure->unwrap(fn () => 'default'))->toBe('default');
});

test('Result concrete classes maintain interface contract', function (): void {
    $success = new Success('test');
    $failure = new Failure(ClientError::Validation->exception());

    // Both should implement ResultInterface
    expect($success)->toBeInstanceOf(OpenFGA\Results\ResultInterface::class);
    expect($failure)->toBeInstanceOf(OpenFGA\Results\ResultInterface::class);

    // Test that all interface methods exist
    $interfaceMethods = [
        'err', 'failed', 'failure', 'recover', 'rethrow',
        'succeeded', 'success', 'then', 'unwrap', 'val',
    ];

    foreach ($interfaceMethods as $method) {
        expect(method_exists($success, $method))->toBeTrue();
        expect(method_exists($failure, $method))->toBeTrue();
    }
});

test('Result unwrap handles complex object structures', function (): void {
    $complexObject = (object) [
        'nested' => [
            'array' => ['value1', 'value2'],
            'object' => (object) ['deep' => true],
        ],
        'number' => 42,
    ];

    $success = new Success($complexObject);
    $failure = new Failure(ClientError::Validation->exception());

    expect($success->unwrap())->toBe($complexObject);
    expect($failure->unwrap(fn () => $complexObject))->toBe($complexObject);
});

test('Result unwrap with closure callback', function (): void {
    $failure = new Failure(ClientError::Validation->exception());

    $result = $failure->unwrap(fn () => 'closure result');
    expect($result)->toBe('closure result');
});
