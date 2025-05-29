<?php

declare(strict_types=1);

use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKey;
use OpenFGA\Requests\{ReadTuplesRequest, ReadTuplesRequestInterface};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

describe('ReadTuplesRequest', function (): void {
    beforeEach(function (): void {
        $this->stream = $this->createMock(StreamInterface::class);
        $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
        $this->streamFactory->method('createStream')->willReturn($this->stream);
    });

    test('implements ReadTuplesRequestInterface', function (): void {
        $tupleKey = new TupleKey('user:anne', 'reader', 'document:123');
        $request = new ReadTuplesRequest('store', $tupleKey);
        expect($request)->toBeInstanceOf(ReadTuplesRequestInterface::class);
    });

    test('constructs with required parameters', function (): void {
        $tupleKey = new TupleKey('user:bob', 'editor', 'folder:reports');

        $request = new ReadTuplesRequest(
            store: 'test-store-id',
            tupleKey: $tupleKey,
        );

        expect($request->getStore())->toBe('test-store-id');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getContinuationToken())->toBeNull();
        expect($request->getPageSize())->toBeNull();
        expect($request->getConsistency())->toBeNull();
    });

    test('constructs with all parameters', function (): void {
        $tupleKey = new TupleKey('user:charlie', 'owner', 'project:alpha');

        $request = new ReadTuplesRequest(
            store: 'prod-store',
            tupleKey: $tupleKey,
            continuationToken: 'next-page-abc123',
            pageSize: 50,
            consistency: Consistency::MINIMIZE_LATENCY,
        );

        expect($request->getStore())->toBe('prod-store');
        expect($request->getTupleKey())->toBe($tupleKey);
        expect($request->getContinuationToken())->toBe('next-page-abc123');
        expect($request->getPageSize())->toBe(50);
        expect($request->getConsistency())->toBe(Consistency::MINIMIZE_LATENCY);
    });

    test('getRequest returns RequestContext with minimal body', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        $request = new ReadTuplesRequest(
            store: 'test-store',
            tupleKey: $tupleKey,
        );

        $context = $request->getRequest($this->streamFactory);

        expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
        expect($context->getUrl())->toBe('/stores/test-store/read');
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

        expect($capturedBody)->toHaveKeys(['tuple_key']);
        expect($capturedBody['tuple_key'])->toBe([
            'user' => 'user:test',
            'relation' => 'viewer',
            'object' => 'doc:1',
        ]);
    });

    test('getRequest returns RequestContext with full body', function (): void {
        $tupleKey = new TupleKey('user:alice', 'admin', 'system:core');

        $request = new ReadTuplesRequest(
            store: 'main-store',
            tupleKey: $tupleKey,
            continuationToken: 'token-xyz',
            pageSize: 25,
            consistency: Consistency::HIGHER_CONSISTENCY,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody)->toHaveKeys(['tuple_key', 'consistency', 'page_size', 'continuation_token']);
        expect($capturedBody['tuple_key'])->toBe([
            'user' => 'user:alice',
            'relation' => 'admin',
            'object' => 'system:core',
        ]);
        expect($capturedBody['consistency'])->toBe('HIGHER_CONSISTENCY');
        expect($capturedBody['page_size'])->toBe(25);
        expect($capturedBody['continuation_token'])->toBe('token-xyz');
    });

    test('throws exception for invalid pageSize', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pageSize must be a positive integer.');
        new ReadTuplesRequest(
            store: 'store',
            tupleKey: $tupleKey,
            pageSize: 0,
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$pageSize must be a positive integer.');
        new ReadTuplesRequest(
            store: 'store',
            tupleKey: $tupleKey,
            pageSize: -10,
        );
    });

    test('throws exception for empty continuationToken', function (): void {
        $tupleKey = new TupleKey('user:test', 'viewer', 'doc:1');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('$continuationToken cannot be an empty string.');
        new ReadTuplesRequest(
            store: 'store',
            tupleKey: $tupleKey,
            continuationToken: '',
        );
    });

    test('handles tuple key with condition', function (): void {
        $condition = $this->createMock(OpenFGA\Models\ConditionInterface::class);
        $condition->method('jsonSerialize')->willReturn(['name' => 'in_department']);

        $tupleKey = new TupleKey(
            user: 'user:diana',
            relation: 'can_approve',
            object: 'purchase_order:12345',
            condition: $condition,
        );

        $request = new ReadTuplesRequest(
            store: 'enterprise-store',
            tupleKey: $tupleKey,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody['tuple_key'])->toBe([
            'user' => 'user:diana',
            'relation' => 'can_approve',
            'object' => 'purchase_order:12345',
            'condition' => ['name' => 'in_department'],
        ]);
    });

    test('handles wildcard in tuple key', function (): void {
        $tupleKey = new TupleKey('user:*', 'viewer', 'document:public');

        $request = new ReadTuplesRequest(
            store: 'public-store',
            tupleKey: $tupleKey,
        );

        $capturedBody = null;
        $this->streamFactory->expects($this->once())
            ->method('createStream')
            ->with($this->callback(function ($body) use (&$capturedBody) {
                $capturedBody = json_decode($body, true);

                return true;
            }));

        $request->getRequest($this->streamFactory);

        expect($capturedBody['tuple_key']['user'])->toBe('user:*');
    });

    test('throws when store is empty', function (): void {
        $this->expectException(InvalidArgumentException::class);
        new ReadTuplesRequest(store: '', tupleKey: new TupleKey('user:test', 'reader', 'doc:1'));
    });
});
