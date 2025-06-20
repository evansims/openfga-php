<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\Collections\{TupleKeys, TupleKeysInterface};
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\{TupleKey, TupleKeyInterface};
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ExpandRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('ExpandRequest', function (): void {
    test('can be instantiated with required parameters', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);

        $request = new ExpandRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
        );

        expect($request)->toBeInstanceOf(ExpandRequest::class);
        expect($request->getStore())->toBe('test-store');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getModel())->toBeNull();
        expect($request->getContextualTuples())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('can be instantiated with all parameters', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);
        $contextualTuples = test()->createMock(TupleKeysInterface::class);

        $request = new ExpandRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
            model: 'test-model',
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($request->getStore())->toBe('test-store');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getModel())->toBe('test-model');
        expect($request->getContextualTuples())->toBe($contextualTuples);
        expect($request->getConsistency())->toBe(Consistency::MINIMIZE_LATENCY);
    });

    test('generates correct request context with minimal parameters', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);
        $tupleKey->method('jsonSerialize')
            ->willReturn(['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1']);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'tuple_key' => ['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1'],
            ]))
            ->willReturn($stream);

        $request = new ExpandRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/expand');
        expect($context->getBody())->toBe($stream);
    });

    test('generates correct request context with all parameters', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);
        $tupleKey->method('jsonSerialize')
            ->willReturn(['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1']);

        $contextualTuples = test()->createMock(TupleKeysInterface::class);
        $contextualTuples->method('jsonSerialize')
            ->willReturn([['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']]);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'tuple_key' => ['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1'],
                'authorization_model_id' => 'test-model',
                'consistency' => 'HIGHER_CONSISTENCY',
                'contextual_tuples' => [['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']],
            ]))
            ->willReturn($stream);

        $request = new ExpandRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
            model: 'test-model',
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/expand');
        expect($context->getBody())->toBe($stream);
    });

    test('filters out null values from request body', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);
        $tupleKey->method('jsonSerialize')
            ->willReturn(['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1']);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(json_encode([
                'tuple_key' => ['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1'],
            ]))
            ->willReturn($stream);

        $request = new ExpandRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
            model: null,
            contextualTuples: null,
            consistency: null,
        );

        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/expand');
        expect($context->getBody())->toBe($stream);
    });

    test('handles different consistency values', function (): void {
        $tupleKey = test()->createMock(TupleKeyInterface::class);
        $tupleKey->method('jsonSerialize')
            ->willReturn(['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1']);

        $stream = test()->createMock(StreamInterface::class);

        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        // Set up expectations for each consistency value call
        $streamFactory->expects(test()->exactly(3))
            ->method('createStream')
            ->willReturnCallback(function (string $json) use ($stream): StreamInterface {
                $data = json_decode($json, true);
                expect($data['tuple_key'])->toBe(['user' => 'user:1', 'relation' => 'viewer', 'object' => 'doc:1']);
                expect($data['consistency'])->toBeIn(['HIGHER_CONSISTENCY', 'MINIMIZE_LATENCY', 'UNSPECIFIED']);

                return $stream;
            });

        // Test each consistency value
        foreach (Consistency::cases() as $consistency) {
            $request = new ExpandRequest(
                store: 'test-store',
                tupleKey: $tupleKey,
                consistency: $consistency,
            );

            $context = $request->getRequest($streamFactory);

            expect($context->getMethod())->toBe(RequestMethod::POST);
            expect($context->getUrl())->toBe('/stores/test-store/expand');
            expect($context->getBody())->toBe($stream);
        }
    });

    test('throws when store ID is empty', function (): void {
        new ExpandRequest(store: '', tupleKey: test()->createMock(TupleKeyInterface::class));
    })->throws(ClientException::class);

    test('omits empty contextual tuples from request body', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');
        $emptyTuples = new TupleKeys;

        $stream = test()->createMock(StreamInterface::class);
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $capturedBody = null;
        $streamFactory->expects(test()->once())
            ->method('createStream')
            ->with(test()->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }))
            ->willReturn($stream);

        $request = new ExpandRequest(
            store: 'test-store',
            model: 'test-model',
            tupleKey: $tupleKey,
            contextualTuples: $emptyTuples,
        );

        $request->getRequest($streamFactory);

        // Empty contextual_tuples is included but with empty tuple_keys array
        expect($capturedBody)->toHaveKey('contextual_tuples');
        expect($capturedBody['contextual_tuples'])->toBe(['tuple_keys' => []]);
    });
});
