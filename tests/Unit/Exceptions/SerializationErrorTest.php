<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\{ClientThrowable, SerializationError, SerializationException};
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

use function count;

describe('SerializationError', function (): void {
    describe('exception() factory method', function (): void {
        /*
         * @param SerializationError $serializationErrorCase
         */
        test('creates SerializationException with all parameters', function (SerializationError $serializationErrorCase): void {
            $mockRequest = new Request;
            $mockResponse = new Response;
            $context = ['detail' => 'some additional detail', 'code' => 123];
            $previousThrowable = new RuntimeException('Previous error');

            $exception = $serializationErrorCase->exception($mockRequest, $mockResponse, $context, $previousThrowable);

            expect($exception)->toBeInstanceOf(SerializationException::class)
                ->and($exception)->toBeInstanceOf(ClientThrowable::class)
                ->and($exception->kind())->toBe($serializationErrorCase)
                ->and($exception->request())->toBe($mockRequest)
                ->and($exception->response())->toBe($mockResponse)
                ->and($exception->context())->toBe($context)
                ->and($exception->getPrevious())->toBe($previousThrowable);
        })->with(SerializationError::cases());

        /*
         * @param SerializationError $serializationErrorCase
         */
        test('creates SerializationException with default parameters', function (SerializationError $serializationErrorCase): void {
            $exception = $serializationErrorCase->exception();

            expect($exception)->toBeInstanceOf(SerializationException::class)
                ->and($exception)->toBeInstanceOf(ClientThrowable::class)
                ->and($exception->kind())->toBe($serializationErrorCase)
                ->and($exception->request())->toBeNull()
                ->and($exception->response())->toBeNull()
                ->and($exception->context())->toBe([])
                ->and($exception->getPrevious())->toBeNull();
        })->with(SerializationError::cases());
    });

    describe('isCollectionError()', function (): void {
        test('collection-related errors return true', function (): void {
            expect(SerializationError::CouldNotAddItemsToCollection->isCollectionError())->toBeTrue();
            expect(SerializationError::EmptyCollection->isCollectionError())->toBeTrue();
            expect(SerializationError::InvalidItemType->isCollectionError())->toBeTrue();
            expect(SerializationError::UndefinedItemType->isCollectionError())->toBeTrue();
        });

        test('non-collection errors return false', function (): void {
            expect(SerializationError::MissingRequiredConstructorParameter->isCollectionError())->toBeFalse();
            expect(SerializationError::Response->isCollectionError())->toBeFalse();
        });

        test('all enum cases are properly classified', function (): void {
            $collectionErrors = [
                SerializationError::CouldNotAddItemsToCollection,
                SerializationError::EmptyCollection,
                SerializationError::InvalidItemType,
                SerializationError::UndefinedItemType,
            ];

            $nonCollectionErrors = [
                SerializationError::MissingRequiredConstructorParameter,
                SerializationError::Response,
            ];

            foreach ($collectionErrors as $error) {
                expect($error->isCollectionError())->toBeTrue("Error {$error->value} should be a collection error");
            }

            foreach ($nonCollectionErrors as $error) {
                expect($error->isCollectionError())->toBeFalse("Error {$error->value} should not be a collection error");
            }

            // Verify we've covered all cases
            $allCases = SerializationError::cases();
            $testedCases = array_merge($collectionErrors, $nonCollectionErrors);
            expect($testedCases)->toHaveCount(count($allCases));
        });
    });

    describe('isTypeValidationError()', function (): void {
        test('type validation errors return true', function (): void {
            expect(SerializationError::InvalidItemType->isTypeValidationError())->toBeTrue();
            expect(SerializationError::UndefinedItemType->isTypeValidationError())->toBeTrue();
            expect(SerializationError::MissingRequiredConstructorParameter->isTypeValidationError())->toBeTrue();
        });

        test('non-type validation errors return false', function (): void {
            expect(SerializationError::CouldNotAddItemsToCollection->isTypeValidationError())->toBeFalse();
            expect(SerializationError::EmptyCollection->isTypeValidationError())->toBeFalse();
            expect(SerializationError::Response->isTypeValidationError())->toBeFalse();
        });

        test('all enum cases are properly classified', function (): void {
            $typeValidationErrors = [
                SerializationError::InvalidItemType,
                SerializationError::UndefinedItemType,
                SerializationError::MissingRequiredConstructorParameter,
            ];

            $nonTypeValidationErrors = [
                SerializationError::CouldNotAddItemsToCollection,
                SerializationError::EmptyCollection,
                SerializationError::Response,
            ];

            foreach ($typeValidationErrors as $error) {
                expect($error->isTypeValidationError())->toBeTrue("Error {$error->value} should be a type validation error");
            }

            foreach ($nonTypeValidationErrors as $error) {
                expect($error->isTypeValidationError())->toBeFalse("Error {$error->value} should not be a type validation error");
            }

            // Verify we've covered all cases
            $allCases = SerializationError::cases();
            $testedCases = array_merge($typeValidationErrors, $nonTypeValidationErrors);
            expect($testedCases)->toHaveCount(count($allCases));
        });
    });

    describe('error categorization overlap', function (): void {
        test('some errors can be both collection and type validation errors', function (): void {
            // InvalidItemType and UndefinedItemType are both collection and type validation errors
            expect(SerializationError::InvalidItemType->isCollectionError())->toBeTrue();
            expect(SerializationError::InvalidItemType->isTypeValidationError())->toBeTrue();

            expect(SerializationError::UndefinedItemType->isCollectionError())->toBeTrue();
            expect(SerializationError::UndefinedItemType->isTypeValidationError())->toBeTrue();
        });

        test('some errors are only collection errors', function (): void {
            expect(SerializationError::CouldNotAddItemsToCollection->isCollectionError())->toBeTrue();
            expect(SerializationError::CouldNotAddItemsToCollection->isTypeValidationError())->toBeFalse();

            expect(SerializationError::EmptyCollection->isCollectionError())->toBeTrue();
            expect(SerializationError::EmptyCollection->isTypeValidationError())->toBeFalse();
        });

        test('some errors are only type validation errors', function (): void {
            expect(SerializationError::MissingRequiredConstructorParameter->isTypeValidationError())->toBeTrue();
            expect(SerializationError::MissingRequiredConstructorParameter->isCollectionError())->toBeFalse();
        });

        test('some errors are neither category', function (): void {
            expect(SerializationError::Response->isCollectionError())->toBeFalse();
            expect(SerializationError::Response->isTypeValidationError())->toBeFalse();
        });
    });

    describe('enum completeness and values', function (): void {
        test('all expected serialization error cases exist', function (): void {
            $expectedErrorValues = [
                'could_not_add_items_to_collection',
                'empty_collection',
                'invalid_item_type',
                'missing_required_constructor_parameter',
                'response',
                'undefined_item_type',
            ];

            $actualValues = array_map(fn (SerializationError $error) => $error->value, SerializationError::cases());

            foreach ($expectedErrorValues as $expectedValue) {
                expect($actualValues)->toContain($expectedValue);
            }

            expect(SerializationError::cases())->toHaveCount(count($expectedErrorValues));
        });

        test('string values match expected error identifiers', function (): void {
            expect(SerializationError::CouldNotAddItemsToCollection->value)->toBe('could_not_add_items_to_collection');
            expect(SerializationError::EmptyCollection->value)->toBe('empty_collection');
            expect(SerializationError::InvalidItemType->value)->toBe('invalid_item_type');
            expect(SerializationError::MissingRequiredConstructorParameter->value)->toBe('missing_required_constructor_parameter');
            expect(SerializationError::Response->value)->toBe('response');
            expect(SerializationError::UndefinedItemType->value)->toBe('undefined_item_type');
        });

        test('errors can be created from string values', function (): void {
            expect(SerializationError::from('could_not_add_items_to_collection'))->toBe(SerializationError::CouldNotAddItemsToCollection);
            expect(SerializationError::from('empty_collection'))->toBe(SerializationError::EmptyCollection);
            expect(SerializationError::from('invalid_item_type'))->toBe(SerializationError::InvalidItemType);
            expect(SerializationError::from('response'))->toBe(SerializationError::Response);
        });
    });

    describe('practical usage scenarios', function (): void {
        test('can identify appropriate error handling strategy', function (): void {
            foreach (SerializationError::cases() as $error) {
                $isCollection = $error->isCollectionError();
                $isTypeValidation = $error->isTypeValidationError();

                // Collection errors need different handling than response errors
                if ($isCollection) {
                    expect($error)->not->toBe(SerializationError::Response);
                }

                // Type validation errors indicate data structure problems
                if ($isTypeValidation) {
                    expect($error)->toBeIn([
                        SerializationError::InvalidItemType,
                        SerializationError::UndefinedItemType,
                        SerializationError::MissingRequiredConstructorParameter,
                    ]);
                }
            }
        });

        test('error categorization supports debugging workflows', function (): void {
            // Collection errors suggest problems with array/collection manipulation
            $collectionErrors = array_filter(
                SerializationError::cases(),
                fn (SerializationError $error) => $error->isCollectionError(),
            );

            expect($collectionErrors)->toContain(SerializationError::EmptyCollection);
            expect($collectionErrors)->toContain(SerializationError::CouldNotAddItemsToCollection);

            // Type validation errors suggest schema or type mismatch issues
            $typeErrors = array_filter(
                SerializationError::cases(),
                fn (SerializationError $error) => $error->isTypeValidationError(),
            );

            expect($typeErrors)->toContain(SerializationError::InvalidItemType);
            expect($typeErrors)->toContain(SerializationError::MissingRequiredConstructorParameter);
        });

        test('all errors can create exceptions successfully', function (): void {
            foreach (SerializationError::cases() as $error) {
                $exception = $error->exception();

                expect($exception)->toBeInstanceOf(SerializationException::class);
                expect($exception->kind())->toBe($error);
                expect($exception->getMessage())->toBeString();
                expect($exception->getMessage())->not->toBeEmpty();
            }
        });
    });
});
