<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Exceptions;

use Exception;
use OpenFGA\Exceptions\{ClientThrowable, DefaultMessages, SerializationError, SerializationException};
use OpenFGA\Translation\Translator;
use PsrMock\Psr7\{Request, Response};
use RuntimeException;

describe('SerializationException', function (): void {
    test('constructs with all parameters', function (): void {
        $kind = SerializationError::InvalidItemType;
        $request = new Request;
        $response = new Response;
        $context = ['field' => 'userset', 'expected' => 'string', 'actual' => 'array'];
        $previous = new RuntimeException('Validation failed');

        $exception = new SerializationException($kind, $request, $response, $context, $previous);

        expect($exception)->toBeInstanceOf(SerializationException::class)
            ->and($exception)->toBeInstanceOf(ClientThrowable::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context())->toBe($context)
            ->and($exception->getPrevious())->toBe($previous);
    });

    test('constructs with minimal parameters', function (): void {
        $kind = SerializationError::Response;

        $exception = new SerializationException($kind);

        expect($exception)->toBeInstanceOf(SerializationException::class)
            ->and($exception->kind())->toBe($kind)
            ->and($exception->request())->toBeNull()
            ->and($exception->response())->toBeNull()
            ->and($exception->context())->toBe([])
            ->and($exception->getPrevious())->toBeNull();
    });

    test('uses custom message from context', function (): void {
        $customMessage = 'Custom serialization error message';
        $context = ['message' => $customMessage];

        $exception = new SerializationException(SerializationError::EmptyCollection, context: $context);

        expect($exception->getMessage())->toBe($customMessage);
    });

    test('generates default message when no custom message provided', function (): void {
        $kind = SerializationError::MissingRequiredConstructorParameter;
        $context = ['parameter' => 'name', 'class' => 'Store'];

        $exception = new SerializationException($kind, context: $context);

        $expectedMessage = Translator::trans(DefaultMessages::forSerializationError($kind), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('removes message from context parameters for translation', function (): void {
        $context = [
            'message' => '',  // Empty message should trigger default message generation
            'collection' => 'TupleKeys',
            'expected_count' => '> 0',
        ];

        $exception = new SerializationException(SerializationError::EmptyCollection, context: $context);

        // Should not include 'message' key in translation parameters
        $expectedContext = ['collection' => 'TupleKeys', 'expected_count' => '> 0'];
        $expectedMessage = Translator::trans(DefaultMessages::forSerializationError(SerializationError::EmptyCollection), $expectedContext);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('implements ClientThrowable interface methods', function (): void {
        $kind = SerializationError::UndefinedItemType;
        $request = new Request;
        $response = new Response;
        $context = ['class' => 'AuthorizationModel'];

        $exception = new SerializationException($kind, $request, $response, $context);

        expect($exception->kind())->toBe($kind);
        expect($exception->request())->toBe($request);
        expect($exception->response())->toBe($response);
        expect($exception->context())->toBe($context);
    });

    test('preserves exception chaining', function (): void {
        $originalException = new Exception('Collection processing error');
        $serializationException = new SerializationException(SerializationError::CouldNotAddItemsToCollection, previous: $originalException);

        expect($serializationException->getPrevious())->toBe($originalException);
    });

    test('handles all serialization error types', function (SerializationError $errorType): void {
        $exception = new SerializationException($errorType);

        expect($exception->kind())->toBe($errorType);
        expect($exception->getMessage())->toBeString()
            ->and($exception->getMessage())->not->toBeEmpty();
    })->with(SerializationError::cases());

    test('context data is preserved during construction', function (): void {
        $context = [
            'collection' => 'TypeDefinitions',
            'class' => 'TypeDefinition',
            'error' => 'Cannot add null item to collection',
            'item_index' => 2,
            'input_data' => ['id' => 'document', 'relations' => null],
        ];

        $exception = new SerializationException(SerializationError::CouldNotAddItemsToCollection, context: $context);

        expect($exception->context())->toBe($context);
    });

    test('exception code is always zero', function (): void {
        $exception = new SerializationException(SerializationError::InvalidItemType);

        expect($exception->getCode())->toBe(0);
    });

    test('exception message handles translation parameters correctly', function (): void {
        $context = [
            'parameter' => 'typeDefinitions',
            'class' => 'AuthorizationModel',
            'expected_type' => 'TypeDefinitions',
            'actual_type' => 'null',
        ];

        $exception = new SerializationException(SerializationError::MissingRequiredConstructorParameter, context: $context);

        // Should include context parameters in the translated message
        $expectedMessage = Translator::trans(DefaultMessages::forSerializationError(SerializationError::MissingRequiredConstructorParameter), $context);
        expect($exception->getMessage())->toBe($expectedMessage);
    });

    test('works with different request and response types', function (): void {
        $request = new Request;
        $response = new Response;

        $exception = new SerializationException(
            SerializationError::Response,
            $request,
            $response,
            ['response_body' => '{"error": "invalid"}', 'expected_format' => 'valid_json'],
        );

        expect($exception->request())->toBe($request)
            ->and($exception->response())->toBe($response)
            ->and($exception->context()['response_body'])->toBe('{"error": "invalid"}')
            ->and($exception->context()['expected_format'])->toBe('valid_json');
    });

    test('inherits from Exception correctly', function (): void {
        $exception = new SerializationException(SerializationError::EmptyCollection);

        expect($exception)->toBeInstanceOf(Exception::class);
        expect(is_subclass_of($exception, Exception::class))->toBeTrue();
    });

    test('can be caught as generic Exception', function (): void {
        $caught = false;
        $exception = new SerializationException(SerializationError::UndefinedItemType);

        try {
            throw $exception;
        } catch (Exception $e) {
            $caught = true;
            expect($e)->toBe($exception);
        }

        expect($caught)->toBeTrue();
    });

    test('can be caught as ClientThrowable', function (): void {
        $caught = false;
        $exception = new SerializationException(SerializationError::Response);

        try {
            throw $exception;
        } catch (ClientThrowable $e) {
            $caught = true;
            expect($e)->toBe($exception);
        }

        expect($caught)->toBeTrue();
    });

    test('handles collection errors with contextual information', function (): void {
        $context = [
            'collection_type' => 'TupleKeys',
            'attempted_items' => 5,
            'failed_items' => 2,
            'error_details' => 'Invalid tuple key format',
        ];

        $exception = new SerializationException(SerializationError::CouldNotAddItemsToCollection, context: $context);

        expect($exception->context()['collection_type'])->toBe('TupleKeys');
        expect($exception->context()['attempted_items'])->toBe(5);
        expect($exception->context()['failed_items'])->toBe(2);
    });

    test('handles empty collection errors with requirements', function (): void {
        $context = [
            'collection_name' => 'typeDefinitions',
            'operation' => 'create_authorization_model',
            'minimum_required' => 1,
            'provided_count' => 0,
        ];

        $exception = new SerializationException(SerializationError::EmptyCollection, context: $context);

        expect($exception->kind())->toBe(SerializationError::EmptyCollection);
        expect($exception->context()['collection_name'])->toBe('typeDefinitions');
        expect($exception->context()['minimum_required'])->toBe(1);
    });

    test('handles invalid item type errors', function (): void {
        $context = [
            'expected_type' => 'TupleKey',
            'actual_type' => 'string',
            'item_value' => 'user:anne#viewer@document:budget',
            'collection' => 'TupleKeys',
        ];

        $exception = new SerializationException(SerializationError::InvalidItemType, context: $context);

        expect($exception->kind())->toBe(SerializationError::InvalidItemType);
        expect($exception->context()['expected_type'])->toBe('TupleKey');
        expect($exception->context()['actual_type'])->toBe('string');
    });
});
