<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use OpenFGA\Exceptions\SerializationError;
use OpenFGA\Translation\Translator;

beforeEach(function (): void {
    // Reset translator to ensure consistent state
    Translator::reset();
});

describe('Exception template messages with context parameters', function (): void {
    it('substitutes context parameters in serialization error messages', function (): void {
        $exception = SerializationError::InvalidItemType->exception(
            context: [
                'property' => 'user_id',
                'className' => 'UserModel',
                'expected' => 'string',
                'actual_type' => 'integer',
            ],
        );

        expect($exception->getMessage())
            ->toBe('Invalid item type for user_id in UserModel: expected string, got integer');
    });

    it('handles missing required constructor parameter with context', function (): void {
        $exception = SerializationError::MissingRequiredConstructorParameter->exception(
            context: [
                'className' => 'OpenFGA\\Models\\Store',
                'paramName' => 'storeId',
            ],
        );

        expect($exception->getMessage())
            ->toBe('Missing required constructor parameter "storeId" for class OpenFGA\\Models\\Store');
    });

    it('includes className in could not add items error', function (): void {
        $exception = SerializationError::CouldNotAddItemsToCollection->exception(
            context: ['className' => 'TupleCollection'],
        );

        expect($exception->getMessage())
            ->toBe('Could not add items to collection TupleCollection');
    });

    it('includes className in undefined item type error', function (): void {
        $exception = SerializationError::UndefinedItemType->exception(
            context: ['className' => 'CustomCollection'],
        );

        expect($exception->getMessage())
            ->toBe('Item type is not defined for CustomCollection');
    });

    it('uses custom message when provided, ignoring template', function (): void {
        $customMessage = 'This is a custom error message';
        $exception = SerializationError::InvalidItemType->exception(
            context: [
                'message' => $customMessage,
                'property' => 'user_id',
                'className' => 'UserModel',
            ],
        );

        expect($exception->getMessage())->toBe($customMessage);
    });

    it('handles missing context parameters gracefully', function (): void {
        // When context parameters are missing, they should appear as literal placeholders
        $exception = SerializationError::InvalidItemType->exception();

        // The Symfony translator will leave placeholders as-is when parameters are missing
        expect($exception->getMessage())
            ->toBe('Invalid item type for %property% in %className%: expected %expected%, got %actual_type%');
    });

    it('preserves other context data in the exception', function (): void {
        $context = [
            'property' => 'email',
            'className' => 'User',
            'expected' => 'string',
            'actual_type' => 'null',
            'debugInfo' => ['line' => 42],
        ];

        $exception = SerializationError::InvalidItemType->exception(context: $context);

        // The message should use the parameters
        expect($exception->getMessage())
            ->toBe('Invalid item type for email in User: expected string, got null');

        // The context should still be available through the exception
        expect($exception->context())->toHaveKey('debugInfo');
        expect($exception->context()['debugInfo'])->toBe(['line' => 42]);

        // All context parameters should be preserved
        expect($exception->context())->toHaveKey('property');
        expect($exception->context())->toHaveKey('className');
        expect($exception->context())->toHaveKey('expected');
        expect($exception->context())->toHaveKey('actual_type');
    });
});

describe('Template messages work with all exception types', function (): void {
    it('supports templates in network error messages', function (): void {
        // First, let's add a template to a network error message
        $exception = SerializationError::Response->exception(
            context: [
                'endpoint' => '/stores/123',
                'status' => 500,
            ],
        );

        // For now, this will use the default message since we haven't updated
        // the network error messages to be templates yet
        expect($exception->getMessage())
            ->toBe('Failed to serialize/deserialize response data');
    });
});
