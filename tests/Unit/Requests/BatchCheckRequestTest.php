<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Models\{BatchCheckItem};
use OpenFGA\Models\Collections\BatchCheckItems;
use OpenFGA\Network\{RequestContext, RequestMethod};
use OpenFGA\Requests\BatchCheckRequest;
use Psr\Http\Message\{StreamFactoryInterface, StreamInterface};

use function OpenFGA\{tuple};

beforeEach(function (): void {
    $this->stream = $this->createMock(StreamInterface::class);
    $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
    $this->streamFactory->method('createStream')->willReturn($this->stream);
});

it('creates a batch check request with valid parameters', function (): void {
    $tupleKey = tuple('user:anne', 'reader', 'document:budget');
    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id-1',
    );

    $items = new BatchCheckItems;
    $items->add($item);

    $request = new BatchCheckRequest(
        store: 'test-store-id',
        model: 'test-model-id',
        checks: $items,
    );

    expect($request->getChecks())->toBe($items);

    $requestContext = $request->getRequest($this->streamFactory);
    expect($requestContext)->toBeInstanceOf(RequestContext::class);
    expect($requestContext->getMethod())->toBe(RequestMethod::POST);
    expect($requestContext->getUrl())->toBe('/stores/test-store-id/batch-check');
    expect($requestContext->getBody())->toBe($this->stream);
});

it('builds correct request body content', function (): void {
    $tupleKey = tuple('user:anne', 'reader', 'document:budget');
    $item = new BatchCheckItem(
        tupleKey: $tupleKey,
        correlationId: 'test-correlation-id-1',
    );

    $items = new BatchCheckItems;
    $items->add($item);

    $request = new BatchCheckRequest(
        store: 'test-store-id',
        model: 'test-model-id',
        checks: $items,
    );

    $streamFactory = test()->createMock(StreamFactoryInterface::class);
    $streamFactory->expects(test()->once())
        ->method('createStream')
        ->with(test()->callback(function (string $json): bool {
            $data = json_decode($json, true);

            expect($data)->toHaveKeys(['authorization_model_id', 'checks']);
            expect($data['authorization_model_id'])->toBe('test-model-id');
            expect($data['checks'])->toBeArray();
            expect($data['checks'])->toHaveCount(1);

            return true;
        }))
        ->willReturn(test()->createMock(StreamInterface::class));

    $request->getRequest($streamFactory);
});

it('throws exception for empty store ID', function (): void {
    $items = new BatchCheckItems;

    expect(fn () => new BatchCheckRequest(
        store: '',
        model: 'test-model-id',
        checks: $items,
    ))->toThrow(ClientException::class);
});

it('throws exception for empty model ID', function (): void {
    $items = new BatchCheckItems;

    expect(fn () => new BatchCheckRequest(
        store: 'test-store-id',
        model: '',
        checks: $items,
    ))->toThrow(ClientException::class);
});

it('throws exception for empty checks collection', function (): void {
    $items = new BatchCheckItems;

    expect(fn () => new BatchCheckRequest(
        store: 'test-store-id',
        model: 'test-model-id',
        checks: $items,
    ))->toThrow(ClientException::class);
});
