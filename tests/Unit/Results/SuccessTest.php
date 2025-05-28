<?php

declare(strict_types=1);

use OpenFGA\Exceptions\{ClientError, NetworkError};
use OpenFGA\Results\{Failure, Success};

beforeEach(function (): void {
    $this->testValue = 'test-value';
    $this->testNumber = 42;
    $this->testArray = ['a', 'b', 'c'];
    $this->testObject = (object) ['name' => 'test', 'value' => 123];
    $this->testError = ClientError::Validation->exception();
});

test('Success constructs with value', function (): void {
    $success = new Success($this->testValue);

    expect($success->val())->toBe($this->testValue);
});

test('Success constructs with different value types', function (): void {
    $stringSuccess = new Success($this->testValue);
    $numberSuccess = new Success($this->testNumber);
    $arraySuccess = new Success($this->testArray);
    $objectSuccess = new Success($this->testObject);

    expect($stringSuccess->val())->toBe($this->testValue);
    expect($numberSuccess->val())->toBe($this->testNumber);
    expect($arraySuccess->val())->toBe($this->testArray);
    expect($objectSuccess->val())->toBe($this->testObject);
});

test('Success succeeded returns true', function (): void {
    $success = new Success($this->testValue);

    expect($success->succeeded())->toBeTrue();
});

test('Success failed returns false', function (): void {
    $success = new Success($this->testValue);

    expect($success->failed())->toBeFalse();
});

test('Success val returns the wrapped value', function (): void {
    $success = new Success($this->testValue);

    expect($success->val())->toBe($this->testValue);
});

test('Success err throws LogicException', function (): void {
    $success = new Success($this->testValue);

    expect(fn () => $success->err())
        ->toThrow(LogicException::class, 'Success has no error');
});

test('Success unwrap returns value when no default provided', function (): void {
    $success = new Success($this->testValue);

    expect($success->unwrap())->toBe($this->testValue);
});

test('Success unwrap returns value when default provided', function (): void {
    $success = new Success($this->testValue);
    $default = 'default-value';

    expect($success->unwrap($default))->toBe($this->testValue);
});

test('Success success executes callback and returns self', function (): void {
    $success = new Success($this->testValue);
    $callbackExecuted = false;
    $receivedValue = null;

    $result = $success->success(function ($value) use (&$callbackExecuted, &$receivedValue): void {
        $callbackExecuted = true;
        $receivedValue = $value;
    });

    expect($callbackExecuted)->toBeTrue();
    expect($receivedValue)->toBe($this->testValue);
    expect($result)->toBe($success);
});

test('Success failure does not execute callback and returns self', function (): void {
    $success = new Success($this->testValue);
    $callbackExecuted = false;

    $result = $success->failure(function (): void {
        $callbackExecuted = true;
    });

    expect($callbackExecuted)->toBeFalse();
    expect($result)->toBe($success);
});

test('Success then executes callback and returns result', function (): void {
    $success = new Success($this->testValue);
    $newValue = 'transformed-value';

    $result = $success->then(function ($value) use ($newValue): Success {
        expect($value)->toBe($this->testValue);

        return new Success($newValue);
    });

    expect($result)->toBeInstanceOf(Success::class);
    expect($result->val())->toBe($newValue);
});

test('Success then can return Failure', function (): void {
    $success = new Success($this->testValue);
    $error = ClientError::Validation->exception();

    $result = $success->then(fn (): Failure => new Failure($error));

    expect($result)->toBeInstanceOf(Failure::class);
    expect($result->err())->toBe($error);
    expect($result->err())->toBe($error);
});

test('Success recover does not execute callback and returns self', function (): void {
    $success = new Success($this->testValue);
    $callbackExecuted = false;

    $result = $success->recover(function (): Success {
        $callbackExecuted = true;

        return new Success('recovered');
    });

    expect($callbackExecuted)->toBeFalse();
    expect($result)->toBe($success);
});

test('Success rethrow returns self when no throwable provided', function (): void {
    $success = new Success($this->testValue);

    $result = $success->rethrow();

    expect($result)->toBe($success);
});

test('Success rethrow returns self when throwable provided', function (): void {
    $success = new Success($this->testValue);
    $error = NetworkError::Unexpected->exception();

    $result = $success->rethrow($error);

    expect($result)->toBe($success);
});

test('Success method chaining works correctly', function (): void {
    $success = new Success($this->testValue);
    $executionOrder = [];

    $result = $success
        ->success(function () use (&$executionOrder): void {
            $executionOrder[] = 'success';
        })
        ->failure(function () use (&$executionOrder): void {
            $executionOrder[] = 'failure';
        })
        ->rethrow();

    expect($executionOrder)->toBe(['success']);
    expect($result)->toBe($success);
});

test('Success works with null values', function (): void {
    $success = new Success(null);

    expect($success->val())->toBeNull();
    expect($success->succeeded())->toBeTrue();
    expect($success->unwrap('default'))->toBeNull();
});

test('Success works with falsy values', function (): void {
    $falsyValues = [false, 0, ''];

    foreach ($falsyValues as $value) {
        $success = new Success($value);

        expect($success->val())->toBe($value);
        expect($success->succeeded())->toBeTrue();
        expect($success->unwrap('default'))->toBe($value);
    }
});

test('Success maintains immutability', function (): void {
    $originalArray = ['a', 'b', 'c'];
    $success = new Success($originalArray);

    // Modify the original array
    $originalArray[] = 'd';

    // Success should still have the original value
    expect($success->val())->toBe(['a', 'b', 'c']);
});
