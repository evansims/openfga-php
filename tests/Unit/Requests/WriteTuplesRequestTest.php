<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\TupleKey;
use OpenFGA\Requests\{WriteTuplesRequest, WriteTuplesRequestInterface};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

beforeEach(function (): void {
    $this->stream = $this->createMock(StreamInterface::class);
    $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
    $this->streamFactory->method('createStream')->willReturn($this->stream);
});

test('WriteTuplesRequest implements WriteTuplesRequestInterface', function (): void {
    $request = new WriteTuplesRequest('store', 'model');
    expect($request)->toBeInstanceOf(WriteTuplesRequestInterface::class);
});

test('WriteTuplesRequest constructs with required parameters only', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store-id',
        model: 'model-id-123',
    );

    expect($request->getStore())->toBe('test-store-id');
    expect($request->getModel())->toBe('model-id-123');
    expect($request->getWrites())->toBeNull();
    expect($request->getDeletes())->toBeNull();
});

test('WriteTuplesRequest constructs with writes only', function (): void {
    $writes = new TupleKeys(
        new TupleKey('user:anne', 'viewer', 'document:budget.pdf'),
        new TupleKey('user:bob', 'editor', 'document:budget.pdf'),
    );

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'model-xyz',
        writes: $writes,
    );

    expect($request->getStore())->toBe('test-store');
    expect($request->getModel())->toBe('model-xyz');
    expect($request->getWrites())->toBe($writes);
    expect($request->getDeletes())->toBeNull();
});

test('WriteTuplesRequest constructs with deletes only', function (): void {
    $deletes = new TupleKeys(
        new TupleKey('user:charlie', 'viewer', 'document:old.pdf'),
    );

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'model-abc',
        deletes: $deletes,
    );

    expect($request->getStore())->toBe('test-store');
    expect($request->getModel())->toBe('model-abc');
    expect($request->getWrites())->toBeNull();
    expect($request->getDeletes())->toBe($deletes);
});

test('WriteTuplesRequest constructs with both writes and deletes', function (): void {
    $writes = new TupleKeys(
        new TupleKey('user:diana', 'admin', 'system:core'),
        new TupleKey('user:edward', 'viewer', 'document:new.pdf'),
    );

    $deletes = new TupleKeys(
        new TupleKey('user:frank', 'editor', 'document:deprecated.pdf'),
        new TupleKey('user:grace', 'viewer', 'folder:archive'),
    );

    $request = new WriteTuplesRequest(
        store: 'prod-store',
        model: 'prod-model',
        writes: $writes,
        deletes: $deletes,
    );

    expect($request->getStore())->toBe('prod-store');
    expect($request->getModel())->toBe('prod-model');
    expect($request->getWrites())->toBe($writes);
    expect($request->getDeletes())->toBe($deletes);
});

test('WriteTuplesRequest getRequest returns RequestContext with model only', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'model-123',
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::POST);
    expect($context->getUrl())->toBe('/stores/test-store/write');
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

    expect($capturedBody)->toHaveKeys(['authorization_model_id']);
    expect($capturedBody)->not->toHaveKeys(['writes', 'deletes']);
    expect($capturedBody['authorization_model_id'])->toBe('model-123');
});

test('WriteTuplesRequest getRequest returns RequestContext with writes', function (): void {
    $writes = new TupleKeys(
        new TupleKey('user:test', 'reader', 'doc:1'),
        new TupleKey('user:test2', 'writer', 'doc:2'),
    );

    $request = new WriteTuplesRequest(
        store: 'store-id',
        model: 'model-id',
        writes: $writes,
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody)->toHaveKeys(['authorization_model_id', 'writes']);
    expect($capturedBody)->not->toHaveKey('deletes');
    expect($capturedBody['writes']['tuple_keys'])->toHaveCount(2);
    expect($capturedBody['writes']['tuple_keys'][0])->toBe([
        'user' => 'user:test',
        'relation' => 'reader',
        'object' => 'doc:1',
    ]);
    expect($capturedBody['writes']['tuple_keys'][1])->toBe([
        'user' => 'user:test2',
        'relation' => 'writer',
        'object' => 'doc:2',
    ]);
});

test('WriteTuplesRequest getRequest returns RequestContext with deletes', function (): void {
    $deletes = new TupleKeys(
        new TupleKey('user:remove', 'viewer', 'doc:old'),
    );

    $request = new WriteTuplesRequest(
        store: 'store-id',
        model: 'model-id',
        deletes: $deletes,
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody)->toHaveKeys(['authorization_model_id', 'deletes']);
    expect($capturedBody)->not->toHaveKey('writes');
    expect($capturedBody['deletes'])->toHaveCount(1);
    expect($capturedBody['deletes']['tuple_keys'][0])->toBe([
        'user' => 'user:remove',
        'relation' => 'viewer',
        'object' => 'doc:old',
    ]);
});

test('WriteTuplesRequest getRequest returns RequestContext with both writes and deletes', function (): void {
    $writes = new TupleKeys(
        new TupleKey('user:add', 'editor', 'doc:new'),
    );

    $deletes = new TupleKeys(
        new TupleKey('user:remove', 'editor', 'doc:old'),
    );

    $request = new WriteTuplesRequest(
        store: 'store',
        model: 'model',
        writes: $writes,
        deletes: $deletes,
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody)->toHaveKeys(['authorization_model_id', 'writes', 'deletes']);
    expect($capturedBody['writes'])->toHaveCount(1);
    expect($capturedBody['deletes'])->toHaveCount(1);
});

test('WriteTuplesRequest handles empty TupleKeys collections', function (): void {
    $writes = new TupleKeys();
    $deletes = new TupleKeys();

    $request = new WriteTuplesRequest(
        store: 'store',
        model: 'model',
        writes: $writes,
        deletes: $deletes,
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody['writes']['tuple_keys'])->toBe([]);
    expect($capturedBody['deletes']['tuple_keys'])->toBe([]);
});

test('WriteTuplesRequest handles tuple keys with conditions', function (): void {
    $condition = $this->createMock(OpenFGA\Models\ConditionInterface::class);
    $condition->method('jsonSerialize')->willReturn(['name' => 'amount_under_limit']);

    $writes = new TupleKeys(
        new TupleKey(
            user: 'user:helen',
            relation: 'can_approve',
            object: 'expense:12345',
            condition: $condition,
        ),
    );

    $request = new WriteTuplesRequest(
        store: 'finance-store',
        model: 'finance-model',
        writes: $writes,
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody['writes']['tuple_keys'][0])->toBe([
        'user' => 'user:helen',
        'relation' => 'can_approve',
        'object' => 'expense:12345',
        'condition' => ['name' => 'amount_under_limit'],
    ]);
});

test('WriteTuplesRequest handles UUID format IDs', function (): void {
    $storeId = '550e8400-e29b-41d4-a716-446655440000';
    $modelId = '660e8400-e29b-41d4-a716-446655440001';

    $request = new WriteTuplesRequest(
        store: $storeId,
        model: $modelId,
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getUrl())->toBe("/stores/{$storeId}/write");
});

test('WriteTuplesRequest throws when store is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new WriteTuplesRequest(
        store: '',
        model: 'model-id',
    );
});

test('WriteTuplesRequest throws when model is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new WriteTuplesRequest(
        store: 'store-id',
        model: '',
    );
});
