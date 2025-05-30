<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Network;

use OpenFGA\Network\RequestMethod;

use function count;

describe('RequestMethod enum', function (): void {
    describe('hasRequestBody()', function (): void {
        test('POST has request body', function (): void {
            expect(RequestMethod::POST->hasRequestBody())->toBeTrue();
        });

        test('PUT has request body', function (): void {
            expect(RequestMethod::PUT->hasRequestBody())->toBeTrue();
        });

        test('GET does not have request body', function (): void {
            expect(RequestMethod::GET->hasRequestBody())->toBeFalse();
        });

        test('DELETE does not have request body', function (): void {
            expect(RequestMethod::DELETE->hasRequestBody())->toBeFalse();
        });

        test('request body aligns with HTTP semantics', function (): void {
            // Methods that typically modify data should have request bodies
            $modifyingMethods = [RequestMethod::POST, RequestMethod::PUT];
            foreach ($modifyingMethods as $method) {
                expect($method->hasRequestBody())->toBeTrue();
            }

            // Methods that retrieve or remove typically don't have request bodies
            $nonBodyMethods = [RequestMethod::GET, RequestMethod::DELETE];
            foreach ($nonBodyMethods as $method) {
                expect($method->hasRequestBody())->toBeFalse();
            }
        });
    });

    describe('isIdempotent()', function (): void {
        test('GET is idempotent', function (): void {
            expect(RequestMethod::GET->isIdempotent())->toBeTrue();
        });

        test('PUT is idempotent', function (): void {
            expect(RequestMethod::PUT->isIdempotent())->toBeTrue();
        });

        test('DELETE is idempotent', function (): void {
            expect(RequestMethod::DELETE->isIdempotent())->toBeTrue();
        });

        test('POST is not idempotent', function (): void {
            expect(RequestMethod::POST->isIdempotent())->toBeFalse();
        });

        test('idempotent methods can be safely retried', function (): void {
            $idempotentMethods = [
                RequestMethod::GET,
                RequestMethod::PUT,
                RequestMethod::DELETE,
            ];

            foreach ($idempotentMethods as $method) {
                expect($method->isIdempotent())->toBeTrue();
            }

            // Only POST should not be idempotent
            expect(RequestMethod::POST->isIdempotent())->toBeFalse();
        });

        test('idempotency follows HTTP specification', function (): void {
            // RFC 7231 specifies GET, HEAD, PUT, DELETE, OPTIONS, TRACE as idempotent
            // We only implement GET, PUT, DELETE, POST in this enum
            expect(RequestMethod::GET->isIdempotent())->toBeTrue();
            expect(RequestMethod::PUT->isIdempotent())->toBeTrue();
            expect(RequestMethod::DELETE->isIdempotent())->toBeTrue();
            expect(RequestMethod::POST->isIdempotent())->toBeFalse();
        });
    });

    describe('isSafe()', function (): void {
        test('GET is safe', function (): void {
            expect(RequestMethod::GET->isSafe())->toBeTrue();
        });

        test('POST is not safe', function (): void {
            expect(RequestMethod::POST->isSafe())->toBeFalse();
        });

        test('PUT is not safe', function (): void {
            expect(RequestMethod::PUT->isSafe())->toBeFalse();
        });

        test('DELETE is not safe', function (): void {
            expect(RequestMethod::DELETE->isSafe())->toBeFalse();
        });

        test('only read operations are safe', function (): void {
            // Only GET should be safe (read-only, no side effects)
            expect(RequestMethod::GET->isSafe())->toBeTrue();

            // All modifying operations should not be safe
            $unsafeMethods = [
                RequestMethod::POST,
                RequestMethod::PUT,
                RequestMethod::DELETE,
            ];

            foreach ($unsafeMethods as $method) {
                expect($method->isSafe())->toBeFalse();
            }
        });

        test('safe methods follow HTTP specification', function (): void {
            // RFC 7231: Safe methods are essentially read-only
            // GET, HEAD, OPTIONS, TRACE are safe; POST, PUT, DELETE are not
            expect(RequestMethod::GET->isSafe())->toBeTrue();
            expect(RequestMethod::POST->isSafe())->toBeFalse();
            expect(RequestMethod::PUT->isSafe())->toBeFalse();
            expect(RequestMethod::DELETE->isSafe())->toBeFalse();
        });
    });

    describe('HTTP method property relationships', function (): void {
        test('safe methods are always idempotent', function (): void {
            foreach (RequestMethod::cases() as $method) {
                if ($method->isSafe()) {
                    expect($method->isIdempotent())->toBeTrue();
                }
            }
        });

        test('safe methods do not have request bodies', function (): void {
            foreach (RequestMethod::cases() as $method) {
                if ($method->isSafe()) {
                    // Safe methods are read-only, so they shouldn't need request bodies
                    expect($method->hasRequestBody())->toBeFalse();
                }
            }
        });

        test('properties are consistent across all methods', function (): void {
            $allMethods = RequestMethod::cases();
            expect($allMethods)->toHaveCount(4);

            foreach ($allMethods as $method) {
                // Each method should have consistent boolean responses
                expect($method->isSafe())->toBeIn([true, false]);
                expect($method->isIdempotent())->toBeIn([true, false]);
                expect($method->hasRequestBody())->toBeIn([true, false]);
            }
        });
    });

    describe('method classifications by use case', function (): void {
        test('retrieval methods', function (): void {
            // GET is for retrieving data
            $getMethod = RequestMethod::GET;
            expect($getMethod->isSafe())->toBeTrue();
            expect($getMethod->isIdempotent())->toBeTrue();
            expect($getMethod->hasRequestBody())->toBeFalse();
        });

        test('creation methods', function (): void {
            // POST is typically for creating resources
            $postMethod = RequestMethod::POST;
            expect($postMethod->isSafe())->toBeFalse();
            expect($postMethod->isIdempotent())->toBeFalse();
            expect($postMethod->hasRequestBody())->toBeTrue();
        });

        test('update methods', function (): void {
            // PUT is for updating/replacing resources
            $putMethod = RequestMethod::PUT;
            expect($putMethod->isSafe())->toBeFalse();
            expect($putMethod->isIdempotent())->toBeTrue();
            expect($putMethod->hasRequestBody())->toBeTrue();
        });

        test('deletion methods', function (): void {
            // DELETE is for removing resources
            $deleteMethod = RequestMethod::DELETE;
            expect($deleteMethod->isSafe())->toBeFalse();
            expect($deleteMethod->isIdempotent())->toBeTrue();
            expect($deleteMethod->hasRequestBody())->toBeFalse();
        });
    });

    describe('enum completeness and values', function (): void {
        test('all HTTP method string values are correct', function (): void {
            expect(RequestMethod::GET->value)->toBe('GET');
            expect(RequestMethod::POST->value)->toBe('POST');
            expect(RequestMethod::PUT->value)->toBe('PUT');
            expect(RequestMethod::DELETE->value)->toBe('DELETE');
        });

        test('enum contains expected HTTP methods', function (): void {
            $expectedMethods = ['GET', 'POST', 'PUT', 'DELETE'];
            $actualValues = array_map(fn (RequestMethod $method) => $method->value, RequestMethod::cases());

            foreach ($expectedMethods as $expectedMethod) {
                expect($actualValues)->toContain($expectedMethod);
            }

            expect(RequestMethod::cases())->toHaveCount(count($expectedMethods));
        });

        test('methods can be created from string values', function (): void {
            expect(RequestMethod::from('GET'))->toBe(RequestMethod::GET);
            expect(RequestMethod::from('POST'))->toBe(RequestMethod::POST);
            expect(RequestMethod::from('PUT'))->toBe(RequestMethod::PUT);
            expect(RequestMethod::from('DELETE'))->toBe(RequestMethod::DELETE);
        });

        test('all cases are covered in property tests', function (): void {
            // Ensure every enum case has been tested for all properties
            $allMethods = RequestMethod::cases();

            foreach ($allMethods as $method) {
                // Each method should have deterministic property values
                $safe = $method->isSafe();
                $idempotent = $method->isIdempotent();
                $hasBody = $method->hasRequestBody();

                expect($safe)->toBeIn([true, false]);
                expect($idempotent)->toBeIn([true, false]);
                expect($hasBody)->toBeIn([true, false]);

                // Verify our understanding of each specific method
                match ($method) {
                    RequestMethod::GET => [
                        expect($safe)->toBeTrue(),
                        expect($idempotent)->toBeTrue(),
                        expect($hasBody)->toBeFalse(),
                    ],
                    RequestMethod::POST => [
                        expect($safe)->toBeFalse(),
                        expect($idempotent)->toBeFalse(),
                        expect($hasBody)->toBeTrue(),
                    ],
                    RequestMethod::PUT => [
                        expect($safe)->toBeFalse(),
                        expect($idempotent)->toBeTrue(),
                        expect($hasBody)->toBeTrue(),
                    ],
                    RequestMethod::DELETE => [
                        expect($safe)->toBeFalse(),
                        expect($idempotent)->toBeTrue(),
                        expect($hasBody)->toBeFalse(),
                    ],
                };
            }
        });
    });

    describe('caching and retry implications', function (): void {
        test('safe methods can be cached', function (): void {
            foreach (RequestMethod::cases() as $method) {
                if ($method->isSafe()) {
                    // Safe methods can be cached since they don't have side effects
                    expect($method)->toBe(RequestMethod::GET);
                }
            }
        });

        test('idempotent methods can be automatically retried', function (): void {
            $idempotentMethods = array_filter(
                RequestMethod::cases(),
                fn (RequestMethod $method) => $method->isIdempotent(),
            );

            expect($idempotentMethods)->toContain(RequestMethod::GET);
            expect($idempotentMethods)->toContain(RequestMethod::PUT);
            expect($idempotentMethods)->toContain(RequestMethod::DELETE);
            expect($idempotentMethods)->not->toContain(RequestMethod::POST);
        });

        test('non-idempotent methods require careful retry logic', function (): void {
            $nonIdempotentMethods = array_filter(
                RequestMethod::cases(),
                fn (RequestMethod $method) => ! $method->isIdempotent(),
            );

            expect($nonIdempotentMethods)->toContain(RequestMethod::POST);
            expect($nonIdempotentMethods)->toHaveCount(1);
        });
    });
});
