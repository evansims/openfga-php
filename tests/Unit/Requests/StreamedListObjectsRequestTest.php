<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKey;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Requests\StreamedListObjectsRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('StreamedListObjectsRequest', function (): void {
    test('creates valid request with required parameters', function (): void {
        $request = new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
        );

        expect($request->getStore())->toBe('store-123');
        expect($request->getType())->toBe('document');
        expect($request->getRelation())->toBe('reader');
        expect($request->getUser())->toBe('user:anne');
        expect($request->getModel())->toBeNull();
        expect($request->getContext())->toBeNull();
        expect($request->getContextualTuples())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('creates valid request with all parameters', function (): void {
        $contextualTuples = new TupleKeys([
            new TupleKey('document:budget-2024', 'viewer', 'user:bob'),
        ]);

        $context = (object) ['department' => 'finance'];

        $request = new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
            model: 'model-456',
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($request->getStore())->toBe('store-123');
        expect($request->getType())->toBe('document');
        expect($request->getRelation())->toBe('reader');
        expect($request->getUser())->toBe('user:anne');
        expect($request->getModel())->toBe('model-456');
        expect($request->getContext())->toBe($context);
        expect($request->getContextualTuples())->toBe($contextualTuples);
        expect($request->getConsistency())->toBe(Consistency::MINIMIZE_LATENCY);
    });

    test('validates store is not empty', function (): void {
        expect(fn () => new StreamedListObjectsRequest(
            store: '',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
        ))->toThrow(ClientException::class);
    });

    test('validates type is not empty', function (): void {
        expect(fn () => new StreamedListObjectsRequest(
            store: 'store-123',
            type: '',
            relation: 'reader',
            user: 'user:anne',
        ))->toThrow(ClientException::class);
    });

    test('validates relation is not empty', function (): void {
        expect(fn () => new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: '',
            user: 'user:anne',
        ))->toThrow(ClientException::class);
    });

    test('validates user is not empty', function (): void {
        expect(fn () => new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: '',
        ))->toThrow(ClientException::class);
    });

    test('validates model is not empty when provided', function (): void {
        expect(fn () => new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
            model: '',
        ))->toThrow(ClientException::class);
    });

    test('builds correct HTTP request context', function (): void {
        $contextualTuples = new TupleKeys([
            new TupleKey('document:budget-2024', 'viewer', 'user:bob'),
        ]);

        $context = (object) ['department' => 'finance'];

        $request = new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
            model: 'model-456',
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        $stream = test()->createMock(StreamInterface::class);
        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->willReturn($stream);

        $requestContext = $request->getRequest($streamFactory);

        expect($requestContext)->toBeInstanceOf(RequestContext::class);
        expect($requestContext->getMethod())->toBe(RequestMethod::POST);
        expect($requestContext->getUrl())->toBe('/stores/store-123/streamed-list-objects');
        expect($requestContext->getBody())->toBe($stream);
    });

    test('filters out null values from request body', function (): void {
        $request = new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
            // All optional parameters are null
        );

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(test()->callback(function (string $json): bool {
                $data = json_decode($json, true);

                // Should only contain non-null values
                expect($data)->toHaveKeys(['type', 'relation', 'user']);
                expect($data)->not->toHaveKey('authorization_model_id');
                expect($data)->not->toHaveKey('context');
                expect($data)->not->toHaveKey('contextual_tuples');
                expect($data)->not->toHaveKey('consistency');

                return true;
            }))
            ->willReturn(test()->createMock(StreamInterface::class));

        $request->getRequest($streamFactory);
    });

    test('includes all values when provided', function (): void {
        $contextualTuples = new TupleKeys([
            new TupleKey('document:budget-2024', 'viewer', 'user:bob'),
        ]);

        $context = (object) ['department' => 'finance'];

        $request = new StreamedListObjectsRequest(
            store: 'store-123',
            type: 'document',
            relation: 'reader',
            user: 'user:anne',
            model: 'model-456',
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(test()->callback(function (string $json): bool {
                $data = json_decode($json, true);

                expect($data)->toHaveKeys([
                    'type',
                    'relation',
                    'user',
                    'authorization_model_id',
                    'context',
                    'contextual_tuples',
                    'consistency',
                ]);

                expect($data['type'])->toBe('document');
                expect($data['relation'])->toBe('reader');
                expect($data['user'])->toBe('user:anne');
                expect($data['authorization_model_id'])->toBe('model-456');
                expect($data['context'])->toEqual(['department' => 'finance']);
                expect($data['consistency'])->toBe('MINIMIZE_LATENCY');

                return true;
            }))
            ->willReturn(test()->createMock(StreamInterface::class));

        $request->getRequest($streamFactory);
    });
});
