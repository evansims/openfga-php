<?php

declare(strict_types=1);

use OpenFGA\Exceptions\{ClientError, NetworkError};
use OpenFGA\Results\{Failure, Success};

// Explicitly require the helpers file since autoloading doesn't work for functions
require_once __DIR__ . '/../../../src/Results/Helpers.php';

use function OpenFGA\Results\{err, failure, ok, result, success, unwrap};

beforeEach(function (): void {
    $this->testValue = 'test-value';
    $this->testError = ClientError::Validation->exception();
    $this->networkError = NetworkError::Unexpected->exception();
});

// Tests for result() function
test('result function wraps closure return value in Success', function (): void {
    $closure = fn () => $this->testValue;

    $result = result($closure);

    expect($result)->toBeInstanceOf(Success::class);
    expect($result->val())->toBe($this->testValue);
});

test('result function wraps closure thrown exception in Failure', function (): void {
    $closure = function (): void {
        throw $this->testError;
    };

    $result = result($closure);

    expect($result)->toBeInstanceOf(Failure::class);
    expect($result->err())->toBe($this->testError);
});

test('result function returns ResultInterface when closure returns one', function (): void {
    $expectedResult = new Success($this->testValue);
    $closure = fn () => $expectedResult;

    $result = result($closure);

    expect($result)->toBe($expectedResult);
});

test('result function returns ResultInterface Failure when closure returns one', function (): void {
    $expectedResult = new Failure($this->testError);
    $closure = fn () => $expectedResult;

    $result = result($closure);

    expect($result)->toBe($expectedResult);
});

test('result function unwraps Success ResultInterface parameter', function (): void {
    $success = new Success($this->testValue);

    $result = result($success);

    expect($result)->toBe($this->testValue);
});

test('result function throws error from Failure ResultInterface parameter', function (): void {
    $failure = new Failure($this->testError);

    $exceptionThrown = false;

    try {
        result($failure);
    } catch (Throwable $e) {
        $exceptionThrown = true;
        expect($e)->toBe($this->testError);
    }

    expect($exceptionThrown)->toBeTrue();
});

test('result function handles different exception types in closures', function (): void {
    $exceptions = [
        ClientError::Validation->exception(),
        NetworkError::Unexpected->exception(),
        new Exception('standard exception'),
        new RuntimeException('runtime error'),
    ];

    foreach ($exceptions as $exception) {
        $closure = function () use ($exception): void {
            throw $exception;
        };

        $result = result($closure);

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->err())->toBe($exception);
    }
});

test('result function handles different return types from closures', function (): void {
    $values = [
        'string' => 'test',
        'integer' => 42,
        'float' => 3.14,
        'boolean' => true,
        'array' => ['a', 'b'],
        'object' => (object) ['test' => true],
        'null' => null,
    ];

    foreach ($values as $type => $value) {
        $closure = fn () => $value;
        $result = result($closure);

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe($value);
    }
});

// Tests for unwrap() function
test('unwrap function returns value from Success', function (): void {
    $success = new Success($this->testValue);

    $result = unwrap($success);

    expect($result)->toBe($this->testValue);
});

test('unwrap function returns callback result from Failure', function (): void {
    $failure = new Failure($this->testError);

    $result = unwrap($failure, fn () => 'default-value');

    expect($result)->toBe('default-value');
});

test('unwrap function throws from Failure when no callback', function (): void {
    $failure = new Failure($this->testError);

    expect(fn () => unwrap($failure))
        ->toThrow($this->testError::class);
});

// Tests for success() function
test('success function returns true and executes callback for Success', function (): void {
    $success = new Success($this->testValue);
    $callbackExecuted = false;
    $receivedValue = null;

    $result = success($success, function ($value) use (&$callbackExecuted, &$receivedValue): void {
        $callbackExecuted = true;
        $receivedValue = $value;
    });

    expect($result)->toBeTrue();
    expect($callbackExecuted)->toBeTrue();
    expect($receivedValue)->toBe($this->testValue);
});

test('success function returns false and does not execute callback for Failure', function (): void {
    $failure = new Failure($this->testError);
    $callbackExecuted = false;

    $result = success($failure, function (): void {
        $callbackExecuted = true;
    });

    expect($result)->toBeFalse();
    expect($callbackExecuted)->toBeFalse();
});

test('success function returns true for Success without callback', function (): void {
    $success = new Success($this->testValue);

    $result = success($success);

    expect($result)->toBeTrue();
});

test('success function returns false for Failure without callback', function (): void {
    $failure = new Failure($this->testError);

    $result = success($failure);

    expect($result)->toBeFalse();
});

test('success function with null callback behaves like no callback', function (): void {
    $success = new Success($this->testValue);
    $failure = new Failure($this->testError);

    expect(success($success, null))->toBeTrue();
    expect(success($failure, null))->toBeFalse();
});

// Tests for failure() function
test('failure function returns true and executes callback for Failure', function (): void {
    $failure = new Failure($this->testError);
    $callbackExecuted = false;
    $receivedError = null;

    $result = failure($failure, function ($error) use (&$callbackExecuted, &$receivedError): void {
        $callbackExecuted = true;
        $receivedError = $error;
    });

    expect($result)->toBeTrue();
    expect($callbackExecuted)->toBeTrue();
    expect($receivedError)->toBe($this->testError);
});

test('failure function returns false and does not execute callback for Success', function (): void {
    $success = new Success($this->testValue);
    $callbackExecuted = false;

    $result = failure($success, function (): void {
        $callbackExecuted = true;
    });

    expect($result)->toBeFalse();
    expect($callbackExecuted)->toBeFalse();
});

test('failure function returns true for Failure without callback', function (): void {
    $failure = new Failure($this->testError);

    $result = failure($failure);

    expect($result)->toBeTrue();
});

test('failure function returns false for Success without callback', function (): void {
    $success = new Success($this->testValue);

    $result = failure($success);

    expect($result)->toBeFalse();
});

test('failure function with null callback behaves like no callback', function (): void {
    $success = new Success($this->testValue);
    $failure = new Failure($this->testError);

    expect(failure($success, null))->toBeFalse();
    expect(failure($failure, null))->toBeTrue();
});

// Tests for ok() function
test('ok function creates Success with value', function (): void {
    $result = ok($this->testValue);

    expect($result)->toBeInstanceOf(Success::class);
    expect($result->val())->toBe($this->testValue);
});

test('ok function creates Success with different value types', function (): void {
    $values = [
        'string' => 'test',
        'integer' => 42,
        'float' => 3.14,
        'boolean' => true,
        'array' => ['a', 'b'],
        'object' => (object) ['test' => true],
        'null' => null,
    ];

    foreach ($values as $type => $value) {
        $result = ok($value);

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe($value);
    }
});

// Tests for err() function
test('err function creates Failure with error', function (): void {
    $result = err($this->testError);

    expect($result)->toBeInstanceOf(Failure::class);
    expect($result->err())->toBe($this->testError);
});

test('err function creates Failure with different error types', function (): void {
    $errors = [
        ClientError::Validation->exception(),
        NetworkError::Unexpected->exception(),
        new Exception('standard exception'),
        new RuntimeException('runtime error'),
    ];

    foreach ($errors as $error) {
        $result = err($error);

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->err())->toBe($error);
    }
});

// Integration tests combining multiple helpers
test('helpers work together in complex scenarios', function (): void {
    // Create a chain using multiple helpers
    $closure = fn (): string => 'success value';

    // Use result() to safely execute - force success for predictable test
    $successClosure = fn () => 'success value';
    $result1 = result($successClosure);

    // Use success() and failure() to handle both cases
    $finalValue = null;
    $errorOccurred = false;

    success($result1, function ($value) use (&$finalValue): void {
        $finalValue = $value;
    });

    failure($result1, function () use (&$errorOccurred): void {
        $errorOccurred = true;
    });

    // Verify success path was taken
    expect($finalValue)->toBe('success value');
    expect($errorOccurred)->toBeFalse();

    // Use unwrap() without callback since it's a Success
    $unwrappedValue = unwrap($result1);
    expect($unwrappedValue)->toBe($finalValue);
});

test('helpers maintain type safety', function (): void {
    $stringSuccess = ok('string value');
    $intSuccess = ok(42);
    $arraySuccess = ok(['a', 'b', 'c']);

    expect(unwrap($stringSuccess))->toBeString();
    expect(unwrap($intSuccess))->toBeInt();
    expect(unwrap($arraySuccess))->toBeArray();
});

test('helpers handle edge cases correctly', function (): void {
    // Test with empty values
    $emptyStringSuccess = ok('');
    $zeroSuccess = ok(0);
    $falseSuccess = ok(false);
    $nullSuccess = ok(null);

    expect(success($emptyStringSuccess))->toBeTrue();
    expect(success($zeroSuccess))->toBeTrue();
    expect(success($falseSuccess))->toBeTrue();
    expect(success($nullSuccess))->toBeTrue();

    expect(unwrap($emptyStringSuccess))->toBe('');
    expect(unwrap($zeroSuccess))->toBe(0);
    expect(unwrap($falseSuccess))->toBeFalse();
    expect(unwrap($nullSuccess))->toBeNull();
});
