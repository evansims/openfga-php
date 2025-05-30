<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Results;

use Exception;
use OpenFGA\Exceptions\{ClientError, ClientException, NetworkError, NetworkException};
use OpenFGA\Messages;
use OpenFGA\Results\{Failure, Success};
use RuntimeException;
use Throwable;

describe('Failure', function (): void {
    beforeEach(function (): void {
        $this->testError = ClientError::Validation->exception();
        $this->networkError = NetworkError::Unexpected->exception();
    });

    test('constructs', function (): void {
        $failure = new Failure($this->testError);

        expect($failure->err())->toBe($this->testError);
    });

    test('accepts different error types', function (): void {
        $clientFailure = new Failure($this->testError);
        $networkFailure = new Failure($this->networkError);

        expect($clientFailure->err())->toBe($this->testError);
        expect($networkFailure->err())->toBe($this->networkError);
    });

    test('succeeded', function (): void {
        $failure = new Failure($this->testError);

        expect($failure->succeeded())->toBeFalse();
    });

    test('failed', function (): void {
        $failure = new Failure($this->testError);

        expect($failure->failed())->toBeTrue();
    });

    test('err', function (): void {
        $failure = new Failure($this->testError);

        expect($failure->err())->toBe($this->testError);
    });

    test('val throws ClientException', function (): void {
        $failure = new Failure($this->testError);

        $failure->val();
    })->throws(ClientException::class, trans(Messages::RESULT_FAILURE_NO_VALUE));

    test('unwrap throws error when no callback provided', function (): void {
        $failure = new Failure($this->testError);

        $failure->unwrap();
    })->throws(ClientException::class);

    test('unwrap with callback receives error and returns callback result', function (): void {
        $failure = new Failure($this->testError);

        $result = $failure->unwrap(function ($error) {
            expect($error)->toBe($this->testError);

            return 'handled-error';
        });

        expect($result)->toBe('handled-error');
    });

    test('unwrap callback can transform error to different types', function (): void {
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

    test('unwrap callback can return null', function (): void {
        $failure = new Failure($this->testError);

        $result = $failure->unwrap(fn () => null);

        expect($result)->toBeNull();
    });

    test('unwrap callback receives correct error type', function (): void {
        $clientFailure = new Failure($this->testError);
        $networkFailure = new Failure($this->networkError);

        $clientResult = $clientFailure->unwrap(function ($error) {
            expect($error)->toBeInstanceOf(ClientException::class);

            return 'client-error';
        });

        $networkResult = $networkFailure->unwrap(function ($error) {
            expect($error)->toBeInstanceOf(NetworkException::class);

            return 'network-error';
        });

        expect($clientResult)->toBe('client-error');
        expect($networkResult)->toBe('network-error');
    });

    test('unwrap callback can itself throw an exception', function (): void {
        $failure = new Failure($this->testError);
        $customException = new RuntimeException('Custom exception from callback');

        $failure->unwrap(function () use ($customException): void {
            throw $customException;
        });
    })->throws(RuntimeException::class, 'Custom exception from callback');

    test('success does not execute callback and returns self', function (): void {
        $failure = new Failure($this->testError);
        $callbackExecuted = false;

        $result = $failure->success(function (): void {
            $callbackExecuted = true;
        });

        expect($callbackExecuted)->toBeFalse();
        expect($result)->toBe($failure);
    });

    test('failure executes callback and returns self', function (): void {
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

    test('then does not execute callback and returns self', function (): void {
        $failure = new Failure($this->testError);
        $callbackExecuted = false;

        $result = $failure->then(function (): Success {
            $callbackExecuted = true;

            return new Success('should not happen');
        });

        expect($callbackExecuted)->toBeFalse();
        expect($result)->toBe($failure);
    });

    test('recover executes callback and returns result', function (): void {
        $failure = new Failure($this->testError);
        $recoveredValue = 'recovered-value';

        $result = $failure->recover(function ($error) use ($recoveredValue): Success {
            expect($error)->toBe($this->testError);

            return new Success($recoveredValue);
        });

        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe($recoveredValue);
    });

    test('recover can return another Failure', function (): void {
        $failure = new Failure($this->testError);
        $newError = NetworkError::Server->exception();

        $result = $failure->recover(fn (): Failure => new Failure($newError));

        expect($result)->toBeInstanceOf(Failure::class);
        expect($result->err())->toBe($newError);
    });

    test('recover wraps non-Result return values', function (): void {
        $failure = new Failure($this->testError);

        // Test with string
        $result = $failure->recover(fn ($error) => 'recovered: ' . $error->getMessage());
        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toStartWith('recovered: ');

        // Test with array
        $result = $failure->recover(fn () => ['recovered' => true]);
        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe(['recovered' => true]);

        // Test with null
        $result = $failure->recover(fn () => null);
        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBeNull();

        // Test with number
        $result = $failure->recover(fn () => 42);
        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe(42);

        // Test with object
        $obj = (object) ['recovered' => true];
        $result = $failure->recover(fn () => $obj);
        expect($result)->toBeInstanceOf(Success::class);
        expect($result->val())->toBe($obj);
    });

    test('recover preserves Result return values', function (): void {
        $failure = new Failure($this->testError);

        // Test that existing Result instances are preserved
        $successResult = new Success('recovered-success');
        $result = $failure->recover(fn () => $successResult);
        expect($result)->toBe($successResult);

        $failureResult = new Failure(NetworkError::Server->exception());
        $result = $failure->recover(fn () => $failureResult);
        expect($result)->toBe($failureResult);
    });

    test('rethrow throws the wrapped error when no throwable provided', function (): void {
        $failure = new Failure($this->testError);

        $thrownException = null;

        try {
            $failure->rethrow();
        } catch (Throwable $e) {
            $thrownException = $e;
        }

        expect($thrownException)->toBe($this->testError);
    });

    test('rethrow throws provided throwable when given', function (): void {
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

    test('method chaining works correctly', function (): void {
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

    test('chaining with recover transforms result', function (): void {
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

    test('preserves error information through chains', function (): void {
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

    test('accepts standard exceptions', function (): void {
        $standardError = new Exception('standard exception');
        $failure = new Failure($standardError);

        expect($failure->err())->toBe($standardError);
        expect($failure->failed())->toBeTrue();

        $result = $failure->unwrap(fn () => 'handled');
        expect($result)->toBe('handled');
    });

    test('accepts custom exceptions', function (): void {
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

    test('maintains error immutability', function (): void {
        $failure = new Failure($this->testError);

        // Verify we get the same error instance
        expect($failure->err())->toBe($this->testError);

        // Even if we get the error and try to modify state, failure should be consistent
        $retrievedError = $failure->err();
        expect($retrievedError)->toBe($this->testError);
    });
});
