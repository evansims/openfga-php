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

test('Failure unwrap throws error when no callback provided', function (): void {
    $failure = new Failure($this->testError);

    expect(fn () => $failure->unwrap())
        ->toThrow($this->testError::class);
});

test('Failure unwrap with callback receives error and returns callback result', function (): void {
    $failure = new Failure($this->testError);

    $result = $failure->unwrap(function ($error) {
        expect($error)->toBe($this->testError);

        return 'handled-error';
    });

    expect($result)->toBe('handled-error');
});

test('Failure unwrap callback can transform error to different types', function (): void {
    $failure = new Failure($this->testError);

    // Transform to string
    $stringResult = $failure->unwrap(fn ($error) => $error->getMessage());
    expect($stringResult)->toBeString();

    // Transform to array
    $arrayResult = $failure->unwrap(fn ($error) => ['error' => $error->getMessage()]);
    expect($arrayResult)->toBeArray()
        ->toHaveKey('error');

    // Transform to number
    $numberResult = $failure->unwrap(fn ($error) => $error->getCode());
    expect($numberResult)->toBeInt();

    // Transform to boolean
    $boolResult = $failure->unwrap(fn () => false);
    expect($boolResult)->toBeFalse();
});

test('Failure unwrap callback can return null', function (): void {
    $failure = new Failure($this->testError);

    $result = $failure->unwrap(fn () => null);

    expect($result)->toBeNull();
});

test('Failure unwrap callback receives correct error type', function (): void {
    $clientFailure = new Failure($this->testError);
    $networkFailure = new Failure($this->networkError);

    $clientResult = $clientFailure->unwrap(function ($error) {
        expect($error)->toBeInstanceOf(OpenFGA\Exceptions\ClientException::class);

        return 'client-error';
    });

    $networkResult = $networkFailure->unwrap(function ($error) {
        expect($error)->toBeInstanceOf(OpenFGA\Exceptions\NetworkException::class);

        return 'network-error';
    });

    expect($clientResult)->toBe('client-error');
    expect($networkResult)->toBe('network-error');
});

test('Failure unwrap callback can itself throw an exception', function (): void {
    $failure = new Failure($this->testError);
    $customException = new RuntimeException('Custom exception from callback');

    expect(fn () => $failure->unwrap(function () use ($customException): void {
        throw $customException;
    }))->toThrow(RuntimeException::class, 'Custom exception from callback');
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

    $result = $failure->unwrap(fn () => 'handled');
    expect($result)->toBe('handled');
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
