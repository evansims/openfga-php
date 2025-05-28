<?php

declare(strict_types=1);

use Mockery\MockInterface;
use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ListObjectsRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

it('can be instantiated with required parameters', function (): void {
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

it('can be instantiated with all parameters', function (): void {
    $context = (object) ['key' => 'value'];
    $contextualTuples = Mockery::mock(TupleKeysInterface::class);

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

it('generates correct request context with minimal parameters', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type' => 'document',
            'relation' => 'viewer',
            'user' => 'user:1',
        ]))
        ->andReturn($stream);

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

it('generates correct request context with all parameters', function (): void {
    $contextObj = (object) ['key' => 'value'];
    $contextualTuples = Mockery::mock(TupleKeysInterface::class);
    $contextualTuples->shouldReceive('jsonSerialize')
        ->once()
        ->andReturn([['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']]);

    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type' => 'document',
            'relation' => 'viewer',
            'user' => 'user:1',
            'authorization_model_id' => 'test-model',
            'context' => $contextObj,
            'contextual_tuples' => [['user' => 'user:2', 'relation' => 'editor', 'object' => 'doc:1']],
            'consistency' => 'HIGHER_CONSISTENCY',
        ]))
        ->andReturn($stream);

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

it('handles complex user identifiers', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type' => 'group',
            'relation' => 'member',
            'user' => 'group:engineering#member',
        ]))
        ->andReturn($stream);

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

it('filters out null values from request body', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);
    $streamFactory->shouldReceive('createStream')
        ->once()
        ->with(json_encode([
            'type' => 'document',
            'relation' => 'viewer',
            'user' => 'user:1',
        ]))
        ->andReturn($stream);

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

it('handles different consistency values', function (): void {
    $stream = Mockery::mock(StreamInterface::class);

    /** @var MockInterface&StreamFactoryInterface $streamFactory */
    $streamFactory = Mockery::mock(StreamFactoryInterface::class);

    // Test each consistency value
    foreach (Consistency::cases() as $consistency) {
        $streamFactory->shouldReceive('createStream')
            ->once()
            ->with(json_encode([
                'type' => 'document',
                'relation' => 'viewer',
                'user' => 'user:1',
                'consistency' => $consistency->value,
            ]))
            ->andReturn($stream);

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
