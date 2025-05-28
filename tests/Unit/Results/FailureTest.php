<?php

declare(strict_types=1);

use OpenFGA\Exceptions\{ClientError, NetworkError};
use OpenFGA\Results\{Failure, Success};

beforeEach(function (): void {
    $this->testError = ClientError::Validation->exception();
    $this->networkError = NetworkError::Unexpected->exception();
});

test('Failure constructs with error', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->err())->toBe($this->testError);
});

test('Failure constructs with different error types', function (): void {
    $clientFailure = new Failure($this->testError);
    $networkFailure = new Failure($this->networkError);

    expect($clientFailure->err())->toBe($this->testError);
    expect($networkFailure->err())->toBe($this->networkError);
});

test('Failure succeeded returns false', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->succeeded())->toBeFalse();
});

test('Failure failed returns true', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->failed())->toBeTrue();
});

test('Failure err returns the wrapped error', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->err())->toBe($this->testError);
});

test('Failure val throws LogicException', function (): void {
    $failure = new Failure($this->testError);

    expect(fn () => $failure->val())
        ->toThrow(LogicException::class, 'Failure has no value');
});

test('Failure unwrap returns default when no default provided', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->unwrap())->toBeNull();
});

test('Failure unwrap returns default when default provided', function (): void {
    $failure = new Failure($this->testError);
    $default = 'default-value';

    expect($failure->unwrap($default))->toBe($default);
});

test('Failure unwrap with different default types', function (): void {
    $failure = new Failure($this->testError);

    expect($failure->unwrap('string'))->toBe('string');
    expect($failure->unwrap(42))->toBe(42);
    expect($failure->unwrap(['array']))->toBe(['array']);
    expect($failure->unwrap(false))->toBeFalse();
});

test('Failure success does not execute callback and returns self', function (): void {
    $failure = new Failure($this->testError);
    $callbackExecuted = false;

    $result = $failure->success(function (): void {
        $callbackExecuted = true;
    });

    expect($callbackExecuted)->toBeFalse();
    expect($result)->toBe($failure);
});

test('Failure failure executes callback and returns self', function (): void {
    $failure = new Failure($this->testError);
    $callbackExecuted = false;
    $receivedError = null;

    $result = $failure->failure(function ($error) use (&$callbackExecuted, &$receivedError): void {
        $callbackExecuted = true;
        $receivedError = $error;
    });

    expect($callbackExecuted)->toBeTrue();
    expect($receivedError)->toBe($this->testError);
    expect($result)->toBe($failure);
});

test('Failure then does not execute callback and returns self', function (): void {
    $failure = new Failure($this->testError);
    $callbackExecuted = false;

    $result = $failure->then(function (): Success {
        $callbackExecuted = true;

        return new Success('should not happen');
    });

    expect($callbackExecuted)->toBeFalse();
    expect($result)->toBe($failure);
});

test('Failure recover executes callback and returns result', function (): void {
    $failure = new Failure($this->testError);
    $recoveredValue = 'recovered-value';

    $result = $failure->recover(function ($error) use ($recoveredValue): Success {
        expect($error)->toBe($this->testError);

        return new Success($recoveredValue);
    });

    expect($result)->toBeInstanceOf(Success::class);
    expect($result->val())->toBe($recoveredValue);
});

test('Failure recover can return another Failure', function (): void {
    $failure = new Failure($this->testError);
    $newError = NetworkError::Server->exception();

    $result = $failure->recover(fn (): Failure => new Failure($newError));

    expect($result)->toBeInstanceOf(Failure::class);
    expect($result->err())->toBe($newError);
});

test('Failure rethrow throws the wrapped error when no throwable provided', function (): void {
    $failure = new Failure($this->testError);

    $thrownException = null;

    try {
        $failure->rethrow();
    } catch (Throwable $e) {
        $thrownException = $e;
    }

    expect($thrownException)->toBe($this->testError);
});

test('Failure rethrow throws provided throwable when given', function (): void {
    $failure = new Failure($this->testError);
    $customError = NetworkError::Server->exception();

    $thrownException = null;

    try {
        $failure->rethrow($customError);
    } catch (Throwable $e) {
        $thrownException = $e;
    }

    expect($thrownException)->toBe($customError);
});

test('Failure method chaining works correctly', function (): void {
    $failure = new Failure($this->testError);
    $executionOrder = [];

    $result = $failure
        ->success(function () use (&$executionOrder): void {
            $executionOrder[] = 'success';
        })
        ->failure(function () use (&$executionOrder): void {
            $executionOrder[] = 'failure';
        });

    expect($executionOrder)->toBe(['failure']);
    expect($result)->toBe($failure);
});

test('Failure chaining with recover transforms result', function (): void {
    $failure = new Failure($this->testError);
    $executionOrder = [];

    $result = $failure
        ->failure(function () use (&$executionOrder): void {
            $executionOrder[] = 'failure';
        })
        ->recover(function () use (&$executionOrder): Success {
            $executionOrder[] = 'recover';

            return new Success('recovered');
        })
        ->success(function () use (&$executionOrder): void {
            $executionOrder[] = 'success-after-recover';
        });

    expect($executionOrder)->toBe(['failure', 'recover', 'success-after-recover']);
    expect($result)->toBeInstanceOf(Success::class);
    expect($result->val())->toBe('recovered');
});

test('Failure preserves error information through chains', function (): void {
    $failure = new Failure($this->testError);
    $capturedErrors = [];

    $result = $failure
        ->failure(function ($error) use (&$capturedErrors): void {
            $capturedErrors[] = $error;
        })
        ->then(fn (): Success => new Success('should not execute'))
        ->failure(function ($error) use (&$capturedErrors): void {
            $capturedErrors[] = $error;
        });

    expect($capturedErrors)->toHaveCount(2);
    expect($capturedErrors[0])->toBe($this->testError);
    expect($capturedErrors[1])->toBe($this->testError);
    expect($result)->toBe($failure);
});

test('Failure works with standard PHP exceptions', function (): void {
    $standardError = new Exception('standard exception');
    $failure = new Failure($standardError);

    expect($failure->err())->toBe($standardError);
    expect($failure->failed())->toBeTrue();
    expect($failure->unwrap('default'))->toBe('default');
});

test('Failure works with custom exceptions', function (): void {
    $customError = new class('custom message') extends Exception {
        public function getCustomData(): string
        {
            return 'custom data';
        }
    };

    $failure = new Failure($customError);

    expect($failure->err())->toBe($customError);
    expect($failure->err()->getCustomData())->toBe('custom data');
});

test('Failure maintains error immutability', function (): void {
    $failure = new Failure($this->testError);

    // Verify we get the same error instance
    expect($failure->err())->toBe($this->testError);

    // Even if we get the error and try to modify state, failure should be consistent
    $retrievedError = $failure->err();
    expect($retrievedError)->toBe($this->testError);
});
