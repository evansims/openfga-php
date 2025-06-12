<?php

declare(strict_types=1);

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use OpenFGA\Models\Collections\TupleKeys;
use OpenFGA\Models\{TupleKey};
use OpenFGA\Network\{BatchRequestProcessor, RequestManagerFactory};
use OpenFGA\Requests\WriteTuplesRequest;
use OpenFGA\Responses\WriteTuplesResponse;
use OpenFGA\Results\SuccessInterface;
use PsrMock\Psr18\Client;

beforeEach(function (): void {
    $this->processor = new BatchRequestProcessor(
        new RequestManagerFactory(
            url: 'https://test.example.com',
            authorizationHeader: null,
            httpClient: null,
            httpStreamFactory: null,
            httpRequestFactory: null,
            httpResponseFactory: null,
            telemetry: null,
        ),
    );

    $this->tupleKeys = new TupleKeys([
        new TupleKey('user:alice', 'reader', 'document:doc1'),
        new TupleKey('user:bob', 'writer', 'document:doc2'),
        new TupleKey('user:charlie', 'admin', 'document:doc3'),
    ]);
});

it('constructs with request manager factory', function (): void {
    expect($this->processor)->toBeInstanceOf(BatchRequestProcessor::class);
});

it('returns null for last request and response initially', function (): void {
    expect($this->processor->getLastRequest())->toBeNull();
    expect($this->processor->getLastResponse())->toBeNull();
});

it('processes empty request successfully', function (): void {
    $emptyRequest = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
    );

    $result = $this->processor->process($emptyRequest);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response)->toBeInstanceOf(WriteTuplesResponse::class);
    expect($response->getTotalOperations())->toBe(0);
    expect($response->getTotalChunks())->toBe(0);
    expect($response->getSuccessfulChunks())->toBe(0);
    expect($response->getFailedChunks())->toBe(0);
});

it('processes empty transactional request successfully', function (): void {
    $emptyRequest = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        transactional: true,
    );

    $result = $this->processor->process($emptyRequest);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->isTransactional())->toBeTrue();
    expect($response->getTotalOperations())->toBe(0);
    expect($response->getTotalChunks())->toBe(0);
});

it('handles transactional request with writes gracefully', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $this->tupleKeys,
        transactional: true,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->isTransactional())->toBeTrue();
    expect($response->getTotalOperations())->toBe(3);
});

it('handles non-transactional request with writes gracefully', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $this->tupleKeys,
        transactional: false,
        maxTuplesPerChunk: 2,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->isTransactional())->toBeFalse();
    expect($response->getTotalOperations())->toBe(3);
});

it('handles non-transactional request with deletes gracefully', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        deletes: $this->tupleKeys,
        transactional: false,
        maxTuplesPerChunk: 1,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->isTransactional())->toBeFalse();
    expect($response->getTotalOperations())->toBe(3);
});

it('handles non-transactional request with both writes and deletes', function (): void {
    $writeKeys = new TupleKeys([
        new TupleKey('user:alice', 'reader', 'document:doc1'),
    ]);

    $deleteKeys = new TupleKeys([
        new TupleKey('user:bob', 'writer', 'document:doc2'),
    ]);

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $writeKeys,
        deletes: $deleteKeys,
        transactional: false,
        maxTuplesPerChunk: 1,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->isTransactional())->toBeFalse();
    expect($response->getTotalOperations())->toBe(2);
});

it('handles requests with different chunk sizes', function (): void {
    $largeKeys = new TupleKeys([
        new TupleKey('user:alice', 'reader', 'document:doc1'),
        new TupleKey('user:bob', 'writer', 'document:doc2'),
        new TupleKey('user:charlie', 'admin', 'document:doc3'),
        new TupleKey('user:dave', 'reader', 'document:doc4'),
        new TupleKey('user:eve', 'writer', 'document:doc5'),
    ]);

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $largeKeys,
        transactional: false,
        maxTuplesPerChunk: 2,  // This should create 3 chunks
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(5);
    expect($response->isTransactional())->toBeFalse();
});

it('handles requests with retry configuration', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $this->tupleKeys,
        transactional: false,
        maxTuplesPerChunk: 1,
        maxRetries: 3,
        retryDelaySeconds: 0.1,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(3);
});

it('handles requests with parallel processing configuration', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $this->tupleKeys,
        transactional: false,
        maxParallelRequests: 2,
        maxTuplesPerChunk: 1,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(3);
});

it('handles requests with stop on first error configuration', function (): void {
    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $this->tupleKeys,
        transactional: false,
        maxTuplesPerChunk: 1,
        stopOnFirstError: true,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(3);
});

it('can be constructed with different factory configurations', function (): void {
    $factory = new RequestManagerFactory(
        url: 'https://different.example.com',
        authorizationHeader: 'Bearer test-token',
        httpClient: null,
        httpStreamFactory: null,
        httpRequestFactory: null,
        httpResponseFactory: null,
        telemetry: null,
        defaultMaxRetries: 5,
    );

    $processor = new BatchRequestProcessor($factory);

    expect($processor)->toBeInstanceOf(BatchRequestProcessor::class);
    expect($processor->getLastRequest())->toBeNull();
    expect($processor->getLastResponse())->toBeNull();
});

it('processes large batches with appropriate chunking', function (): void {
    // Create a large batch that will exceed chunk limits
    $largeBatch = [];

    for ($i = 0; 250 > $i; $i++) {
        $largeBatch[] = new TupleKey("user:user{$i}", 'reader', "document:doc{$i}");
    }

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: new TupleKeys($largeBatch),
        transactional: false,
        maxTuplesPerChunk: 100,  // Standard max chunk size
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(250);
    expect($response->isTransactional())->toBeFalse();
});

it('handles edge case with single tuple operation', function (): void {
    $singleKey = new TupleKeys([
        new TupleKey('user:single', 'reader', 'document:single'),
    ]);

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: $singleKey,
        transactional: false,
        maxTuplesPerChunk: 100,
    );

    $result = $this->processor->process($request);

    expect($result)->toBeInstanceOf(SuccessInterface::class);

    $response = $result->unwrap();
    expect($response->getTotalOperations())->toBe(1);
});

it('treats 207 response as success', function (): void {
    $psr17 = new Psr17Factory;

    $mockClient = new Client([
        'POST https://test.example.com/stores/test-store/write' => new Response(207),
    ]);

    $factory = new RequestManagerFactory(
        url: 'https://test.example.com',
        authorizationHeader: null,
        httpClient: $mockClient,
        httpStreamFactory: $psr17,
        httpRequestFactory: $psr17,
        httpResponseFactory: $psr17,
        telemetry: null,
    );

    $processor = new BatchRequestProcessor($factory);

    $request = new WriteTuplesRequest(
        store: 'test-store',
        model: 'test-model',
        writes: new TupleKeys([
            new TupleKey('user:anne', 'reader', 'document:one'),
        ]),
        transactional: false,
        maxTuplesPerChunk: 1,
    );

    $result = $processor->process($request);

    $response = $result->unwrap();

    expect($response->getSuccessfulChunks())->toBe(1);
    expect($response->getFailedChunks())->toBe(0);
});
