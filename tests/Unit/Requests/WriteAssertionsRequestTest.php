<?php

declare(strict_types=1);

use OpenFGA\Models\{Assertion, AssertionTupleKey};
use OpenFGA\Models\Collections\{Assertions, TupleKeys};
use OpenFGA\Models\TupleKey;
use OpenFGA\Requests\{WriteAssertionsRequest, WriteAssertionsRequestInterface};
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

beforeEach(function (): void {
    $this->stream = $this->createMock(StreamInterface::class);
    $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
    $this->streamFactory->method('createStream')->willReturn($this->stream);
});

test('WriteAssertionsRequest implements WriteAssertionsRequestInterface', function (): void {
    $assertions = new Assertions();
    $request = new WriteAssertionsRequest($assertions, 'store', 'model');
    expect($request)->toBeInstanceOf(WriteAssertionsRequestInterface::class);
});

test('WriteAssertionsRequest constructs with required parameters', function (): void {
    $assertions = new Assertions(
        new Assertion(
            tupleKey: new AssertionTupleKey('user:anne', 'viewer', 'document:budget.pdf'),
            expectation: true,
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'editor', 'document:budget.pdf'),
            expectation: false,
        ),
    );

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: 'test-store-id',
        model: 'model-id-123',
    );

    expect($request->getAssertions())->toBe($assertions);
    expect($request->getStore())->toBe('test-store-id');
    expect($request->getModel())->toBe('model-id-123');
});

test('WriteAssertionsRequest getRequest returns RequestContext', function (): void {
    $assertions = new Assertions(
        new Assertion(
            tupleKey: new AssertionTupleKey('user:test', 'reader', 'doc:1'),
            expectation: true,
        ),
    );

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: 'test-store',
        model: 'model-xyz',
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::PUT);
    expect($context->getUrl())->toBe('/stores/test-store/assertions/model-xyz');
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

    expect($capturedBody)->toHaveKeys(['assertions']);
    expect($capturedBody['assertions'])->toHaveCount(1);
    expect($capturedBody['assertions'][0])->toBe([
        'tuple_key' => [
            'user' => 'user:test',
            'relation' => 'reader',
            'object' => 'doc:1',
        ],
        'expectation' => true,
    ]);
});

test('WriteAssertionsRequest handles empty assertions', function (): void {
    $assertions = new Assertions();

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: 'store-id',
        model: 'model-id',
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody['assertions'])->toBe([]);
});

test('WriteAssertionsRequest handles multiple assertions with context', function (): void {
    $contextualTuples = new TupleKeys(
        new TupleKey('user:anne', 'member', 'group:engineering'),
        new TupleKey('group:engineering', 'viewer', 'document:budget.pdf'),
    );

    $assertions = new Assertions(
        new Assertion(
            tupleKey: new AssertionTupleKey('user:anne', 'viewer', 'document:budget.pdf'),
            expectation: true,
            contextualTuples: $contextualTuples,
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:bob', 'editor', 'document:budget.pdf'),
            expectation: false,
            context: ['time_of_day' => 'evening'],
        ),
        new Assertion(
            tupleKey: new AssertionTupleKey('user:charlie', 'owner', 'document:budget.pdf'),
            expectation: true,
        ),
    );

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: 'prod-store',
        model: 'prod-model',
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody['assertions'])->toHaveCount(3);

    // First assertion with contextual tuples
    expect($capturedBody['assertions'][0])->toHaveKeys(['tuple_key', 'expectation', 'contextual_tuples']);
    expect($capturedBody['assertions'][0]['contextual_tuples'])->toHaveCount(2);

    // Second assertion with context
    expect($capturedBody['assertions'][1])->toHaveKeys(['tuple_key', 'expectation', 'context']);
    expect($capturedBody['assertions'][1]['context'])->toBe(['time_of_day' => 'evening']);

    // Third assertion with only required fields
    expect($capturedBody['assertions'][2])->toHaveKeys(['tuple_key', 'expectation']);
    expect($capturedBody['assertions'][2])->not->toHaveKeys(['contextual_tuples', 'context']);
});

test('WriteAssertionsRequest handles UUID format IDs', function (): void {
    $assertions = new Assertions();
    $storeId = '550e8400-e29b-41d4-a716-446655440000';
    $modelId = '660e8400-e29b-41d4-a716-446655440001';

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: $storeId,
        model: $modelId,
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getUrl())->toBe("/stores/{$storeId}/assertions/{$modelId}");
});

test('WriteAssertionsRequest throws when store is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new WriteAssertionsRequest(
        assertions: new Assertions(),
        store: '',
        model: 'model-id',
    );
});

test('WriteAssertionsRequest throws when model is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new WriteAssertionsRequest(
        assertions: new Assertions(),
        store: 'store-id',
        model: '',
    );
});

test('WriteAssertionsRequest preserves assertion order', function (): void {
    $assertions = new Assertions();
    for ($i = 1; $i <= 5; ++$i) {
        $assertions->add(new Assertion(
            tupleKey: new AssertionTupleKey("user:user{$i}", 'viewer', "doc:{$i}"),
            expectation: 0 === $i % 2,
        ));
    }

    $request = new WriteAssertionsRequest(
        assertions: $assertions,
        store: 'store',
        model: 'model',
    );

    $capturedBody = null;
    $this->streamFactory->expects($this->once())
        ->method('createStream')
        ->with($this->callback(function ($body) use (&$capturedBody) {
            $capturedBody = json_decode($body, true);

            return true;
        }));

    $request->getRequest($this->streamFactory);

    expect($capturedBody['assertions'])->toHaveCount(5);
    for ($i = 0; $i < 5; ++$i) {
        $num = $i + 1;
        expect($capturedBody['assertions'][$i]['tuple_key']['user'])->toBe("user:user{$num}");
        expect($capturedBody['assertions'][$i]['expectation'])->toBe(0 === $num % 2);
    }
});
