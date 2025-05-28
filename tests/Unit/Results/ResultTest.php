<?php

declare(strict_types=1);

use OpenFGA\Exceptions\ClientError;
use OpenFGA\Results\{Failure, Result, Success};

test('Result unwrap returns value for Success', function (): void {
    $value = 'test-value';
    $success = new Success($value);

    expect($success->unwrap())->toBe($value);
});

test('Result unwrap returns value for Success with default provided', function (): void {
    $value = 'test-value';
    $default = 'default-value';
    $success = new Success($value);

    expect($success->unwrap($default))->toBe($value);
});

test('Result unwrap returns default for Failure with no default', function (): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    expect($failure->unwrap())->toBeNull();
});

test('Result unwrap returns default for Failure with default provided', function (): void {
    $error = ClientError::Validation->exception();
    $default = 'default-value';
    $failure = new Failure($error);

    expect($failure->unwrap($default))->toBe($default);
});

test('Result unwrap with various default types for Failure', function (mixed $default): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    expect($failure->unwrap($default))->toBe($default);
})->with([
    'string default' => ['default-string'],
    'number default' => [42],
    'array default' => [['a', 'b', 'c']],
    'object default' => [(object) ['key' => 'value']],
    'boolean true default' => [true],
    'boolean false default' => [false],
    'null default' => [null],
]);

test('Result unwrap with falsy defaults for Failure', function (): void {
    $error = ClientError::Validation->exception();
    $failure = new Failure($error);

    // Test various falsy values
    expect($failure->unwrap(false))->toBeFalse();
    expect($failure->unwrap(0))->toBe(0);
    expect($failure->unwrap(''))->toBe('');
    expect($failure->unwrap([]))->toBe([]);
    expect($failure->unwrap(null))->toBeNull();
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
        expect($success->unwrap('default'))->toBe($value);
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
            expect($result->unwrap('default'))->toBe($value);
        } else {
            expect($result->unwrap('default'))->toBe('default');
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
    expect($success->unwrap('default'))->toBe('success-value');
    expect($failure->unwrap('default'))->toBe('default');
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
    expect($failure->unwrap($complexObject))->toBe($complexObject);
});

test('Result unwrap with closure as default', function (): void {
    $closure = fn () => 'closure result';
    $failure = new Failure(ClientError::Validation->exception());

    expect($failure->unwrap($closure))->toBe($closure);
});
