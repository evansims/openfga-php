<?php

declare(strict_types=1);

use OpenFGA\Models\Collections\TupleKeysInterface;
use OpenFGA\Models\Enums\Consistency;
use OpenFGA\Models\TupleKeyInterface;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ExpandRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

it('can be instantiated with required parameters', function (): void {
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

it('can be instantiated with all parameters', function (): void {
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

it('generates correct request context with minimal parameters', function (): void {
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

it('generates correct request context with all parameters', function (): void {
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

it('filters out null values from request body', function (): void {
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

it('handles different consistency values', function (): void {
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
