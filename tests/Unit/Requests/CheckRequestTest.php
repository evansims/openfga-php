<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKey;
use OpenFGA\Requests\{CheckRequest, CheckRequestInterface};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('CheckRequest', function (): void {
    beforeEach(function (): void {
        $this->stream = $this->createMock(StreamInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->streamFactory->method('createStream')->willReturn($this->stream);
    });

    test('implements CheckRequestInterface', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');
        $request = new CheckRequest('store', 'model', $tupleKey);
        expect($request)->toBeInstanceOf(CheckRequestInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tupleKey = new TupleKey('user:anne', 'viewer', 'document:budget.pdf');

        $request = new CheckRequest(
            store: 'test-store',
            model: 'test-model',
            tupleKey: $tupleKey,
        );

        expect($request)->toBeInstanceOf(CheckRequest::class);
        expect($request->getStore())->toBe('test-store');
        expect($request->getAuthorizationModel())->toBe('test-model');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getTrace())->toBeNull();
        expect($request->getContext())->toBeNull();
        expect($request->getContextualTuples())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('constructs with all parameters', function (): void {
        $tupleKey = new TupleKey('user:bob', 'editor', 'document:report.pdf');
        $context = (object) ['ip_address' => '192.168.1.1'];
        $contextualTuples = new TupleKeys(
            new TupleKey('user:alice', 'member', 'group:engineering'),
            new TupleKey('group:engineering', 'viewer', 'document:report.pdf'),
        );

        $request = new CheckRequest(
            store: 'test-store',
            model: 'test-model',
            tupleKey: $tupleKey,
            trace: true,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        expect($request->getStore())->toBe('test-store');
        expect($request->getAuthorizationModel())->toBe('test-model');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getTrace())->toBe(true);
        expect($request->getContext())->toBe($context);
        expect($request->getContextualTuples())->toBe($contextualTuples);
        expect($request->getConsistency())->toBe(Consistency::HIGHER_CONSISTENCY);
    });

    test('getRequest returns RequestContext with minimal parameters', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        $request = new CheckRequest(
            store: 'test-store',
            model: 'test-model',
            tupleKey: $tupleKey,
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/check');
        expect($context->getBody())->toBe($this->stream);
        expect($context->useApiUrl())->toBeTrue();

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody)->toHaveKeys(['tuple_key', 'authorization_model_id']);
        expect($capturedBody['tuple_key'])->toBe([
            'user' => 'user:test',
            'relation' => 'viewer',
            'object' => 'doc:1',
        ]);
        expect($capturedBody['authorization_model_id'])->toBe('test-model');
    });

    test('getRequest returns RequestContext with all parameters', function (): void {
        $tupleKey = new TupleKey('user:charlie', 'admin', 'system:core');
        $context = (object) ['location' => 'US'];
        $contextualTuples = new TupleKeys(
            new TupleKey('user:dave', 'member', 'group:admins'),
        );

        $request = new CheckRequest(
            store: 'main-store',
            model: 'main-model',
            tupleKey: $tupleKey,
            trace: true,
            context: $context,
            contextualTuples: $contextualTuples,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $requestContext = $request->getRequest($this->streamFactory);

        expect($requestContext->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
        expect($requestContext->getUrl())->toBe('/stores/main-store/check');
        expect($requestContext->getBody())->toBe($this->stream);

        expect($capturedBody)->toHaveKeys([
            'tuple_key',
            'authorization_model_id',
            'trace',
            'context',
            'consistency',
            'contextual_tuples',
        ]);
        expect($capturedBody['tuple_key'])->toBe([
            'user' => 'user:charlie',
            'relation' => 'admin',
            'object' => 'system:core',
        ]);
        expect($capturedBody['authorization_model_id'])->toBe('main-model');
        expect($capturedBody['trace'])->toBe(true);
        expect($capturedBody['context'])->toBe(['location' => 'US']);
        expect($capturedBody['consistency'])->toBe('MINIMIZE_LATENCY');
        expect($capturedBody['contextual_tuples'])->toHaveCount(1);
    });

    test('filters out null values from request body', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        $request = new CheckRequest(
            store: 'test-store',
            model: 'test-model',
            tupleKey: $tupleKey,
            trace: false,
            context: null,
            contextualTuples: null,
            consistency: null,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $context = $request->getRequest($this->streamFactory);

        expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/check');
        expect($context->getBody())->toBe($this->stream);

        // Should include trace: false but exclude null values
        expect($capturedBody)->toHaveKeys(['tuple_key', 'authorization_model_id', 'trace']);
        expect($capturedBody)->not->toHaveKeys(['context', 'contextual_tuples', 'consistency']);
        expect($capturedBody['trace'])->toBe(false);
    });

    test('handles different consistency values', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        foreach (Consistency::cases() as $consistency) {
            $request = new CheckRequest(
                store: 'test-store',
                model: 'test-model',
                tupleKey: $tupleKey,
                consistency: $consistency,
            );

            $capturedBody = null;
            $streamFactory = $this->createMock(StreamFactoryInterface::class);
            $stream = $this->createMock(StreamInterface::class);

            $streamFactory->expects($this->once())
                ->method('createStream')
                ->with($this->callback(function ($body) use (&$capturedBody) {
                    $capturedBody = json_decode($body, true);

                    return true;
                }))
                ->willReturn($stream);

            $context = $request->getRequest($streamFactory);

            expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
            expect($context->getUrl())->toBe('/stores/test-store/check');
            expect($context->getBody())->toBe($stream);
            expect($capturedBody['consistency'])->toBe($consistency->value);
        }
    });

    test('throws when store ID is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new CheckRequest(store: '', model: 'test-model', tupleKey: new TupleKey('user:test', 'viewer', 'doc:1'));
    });

    test('throws when model ID is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new CheckRequest(store: 'test-store', model: '', tupleKey: new TupleKey('user:test', 'viewer', 'doc:1'));
    });
});
