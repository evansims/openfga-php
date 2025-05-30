<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use InvalidArgumentException;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ListObjectsRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('ListObjectsRequest', function (): void {
    test('can be instantiated with required parameters', function (): void {
        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'document',
            relation: 'viewer',
            user: 'user:1',
        );

        expect($request)->toBeInstanceOf(ListObjectsRequest::class);
        expect($request->getStore())->toBe('test-store');
        expect($request->getType())->toBe('document');
        expect($request->getRelation())->toBe('viewer');
        expect($request->getUser())->toBe('user:1');
        expect($request->getModel())->toBeNull();
        expect($request->getContext())->toBeNull();
        expect($request->getContextualTuples())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('can be instantiated with all parameters', function (): void {
        $context = (object) ['key' => 'value'];
        $contextualTuples = test()->createMock(TupleKeysInterface::class);

        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'document',
            relation: 'viewer',
            user: 'user:1',
            model: 'test-model',
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        expect($request->getStore())->toBe('test-store');
        expect($request->getType())->toBe('document');
        expect($request->getRelation())->toBe('viewer');
        expect($request->getUser())->toBe('user:1');
        expect($request->getModel())->toBe('test-model');
        expect($request->getContext())->toBe($context);
        expect($request->getContextualTuples())->toBe($contextualTuples);
        expect($request->getConsistency())->toBe(Consistency::HIGHER_CONSISTENCY);
    });

    test('generates correct request context with minimal parameters', function (): void {
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'type' => 'document',
                'relation' => 'viewer',
                'user' => 'user:1',
            ]))
            ->willReturn($stream);

        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'document',
            relation: 'viewer',
            user: 'user:1',
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/list-objects');
        expect($context->getBody())->toBe($stream);
    });

    test('generates correct request context with all parameters', function (): void {
        $contextObj = (object) ['key' => 'value'];
        $contextualTuples = test()->createMock(TupleKeysInterface::class);
        $contextualTuples->method('jsonSerialize')
            ->willReturn([['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']]);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'type' => 'document',
                'relation' => 'viewer',
                'user' => 'user:1',
                'authorization_model_id' => 'test-model',
                'context' => $contextObj,
                'contextual_tuples' => [['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']],
                'consistency' => 'HIGHER_CONSISTENCY',
            ]))
            ->willReturn($stream);

        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'document',
            relation: 'viewer',
            user: 'user:1',
            model: 'test-model',
            context: $contextObj,
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/list-objects');
        expect($context->getBody())->toBe($stream);
    });

    test('handles complex user identifiers', function (): void {
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'type' => 'group',
                'relation' => 'member',
                'user' => 'group:engineering#member',
            ]))
            ->willReturn($stream);

        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'group',
            relation: 'member',
            user: 'group:engineering#member',
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/list-objects');
    });

    test('filters out null values from request body', function (): void {
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'type' => 'document',
                'relation' => 'viewer',
                'user' => 'user:1',
            ]))
            ->willReturn($stream);

        $request = new ListObjectsRequest(
            store: 'test-store',
            type: 'document',
            relation: 'viewer',
            user: 'user:1',
            model: null,
            context: null,
            contextualTuples: null,
            consistency: null,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/list-objects');
        expect($context->getBody())->toBe($stream);
    });

    test('handles different consistency values', function (): void {
        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        // Set up expectations for each consistency value call
        $streamFactory->expects(test()->exactly(3))
            ->method('createStream')
            ->willReturnCallback(function (string $json) use ($stream): StreamInterface {
                $data = json_decode($json, true);
                expect($data['type'])->toBe('document');
                expect($data['relation'])->toBe('viewer');
                expect($data['user'])->toBe('user:1');
                expect($data['consistency'])->toBeIn(['HIGHER_CONSISTENCY', 'MINIMIZE_LATENCY', 'UNSPECIFIED']);

                return $stream;
            });

        // Test each consistency value
        foreach (Consistency::cases() as $consistency) {
            $request = new ListObjectsRequest(
                store: 'test-store',
                type: 'document',
                relation: 'viewer',
                user: 'user:1',
                consistency: $consistency,
            );

            $context = $request->getRequest($streamFactory);

            expect($context->getMethod())->toBe(RequestMethod::POST);
            expect($context->getUrl())->toBe('/stores/test-store/list-objects');
            expect($context->getBody())->toBe($stream);
        }
    });
    test('throws when store ID is empty', function (): void {
        new ListObjectsRequest(store: '', type: 'document', relation: 'viewer', user: 'user:1');
    })->throws(InvalidArgumentException::class);

    test('throws when type is empty', function (): void {
        new ListObjectsRequest(store: 'test-store', type: '', relation: 'viewer', user: 'user:1');
    })->throws(InvalidArgumentException::class);

    test('throws when relation is empty', function (): void {
        new ListObjectsRequest(store: 'test-store', type: 'document', relation: '', user: 'user:1');
    })->throws(InvalidArgumentException::class);

    test('throws when user is empty', function (): void {
        new ListObjectsRequest(store: 'test-store', type: 'document', relation: 'viewer', user: '');
    })->throws(InvalidArgumentException::class);

    test('throws when model is empty', function (): void {
        new ListObjectsRequest(store: 'test-store', type: 'document', relation: 'viewer', user: 'user:1', model: '');
    })->throws(InvalidArgumentException::class);
});
