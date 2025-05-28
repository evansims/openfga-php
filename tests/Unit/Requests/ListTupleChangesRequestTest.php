<?php

declare(strict_types=1);

use OpenFGA\Requests\{ListTupleChangesRequest, ListTupleChangesRequestInterface};
use Psr\Http\Message\StreamFactoryInterface;

beforeEach(function (): void {
    $this->streamFactory = $this->createMock(StreamFactoryInterface::class);
});

test('ListTupleChangesRequest implements ListTupleChangesRequestInterface', function (): void {
    $request = new ListTupleChangesRequest('test-store');
    expect($request)->toBeInstanceOf(ListTupleChangesRequestInterface::class);
});

test('ListTupleChangesRequest constructs with store only', function (): void {
    $request = new ListTupleChangesRequest('test-store');

    expect($request->getStore())->toBe('test-store');
    expect($request->getContinuationToken())->toBeNull();
    expect($request->getPageSize())->toBeNull();
    expect($request->getType())->toBeNull();
    expect($request->getStartTime())->toBeNull();
});

test('ListTupleChangesRequest constructs with all parameters', function (): void {
    $startTime = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('America/New_York'));

    $request = new ListTupleChangesRequest(
        store: 'test-store-id',
        continuationToken: 'next-page-token',
        pageSize: 100,
        type: 'document',
        startTime: $startTime,
    );

    expect($request->getStore())->toBe('test-store-id');
    expect($request->getContinuationToken())->toBe('next-page-token');
    expect($request->getPageSize())->toBe(100);
    expect($request->getType())->toBe('document');
    expect($request->getStartTime())->toBe($startTime);
});

test('ListTupleChangesRequest getRequest returns RequestContext with minimal parameters', function (): void {
    $request = new ListTupleChangesRequest('test-store');
    $context = $request->getRequest($this->streamFactory);

    expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::GET);
    expect($context->getUrl())->toBe('/stores/test-store/changes');
    expect($context->getBody())->toBeNull();
    expect($context->useApiUrl())->toBeTrue();
});

test('ListTupleChangesRequest getRequest returns RequestContext with all parameters', function (): void {
    $startTime = new DateTimeImmutable('2024-01-01 10:00:00', new DateTimeZone('America/New_York'));

    $request = new ListTupleChangesRequest(
        store: 'test-store-id',
        continuationToken: 'next-page-token',
        pageSize: 50,
        type: 'document',
        startTime: $startTime,
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getMethod())->toBe(OpenFGA\Network\RequestMethod::GET);
    expect($context->getUrl())->toContain('/stores/test-store-id/changes?');
    expect($context->getUrl())->toContain('continuation_token=next-page-token');
    expect($context->getUrl())->toContain('page_size=50');
    expect($context->getUrl())->toContain('type=document');
    expect($context->getUrl())->toContain('start_time=' . urlencode('2024-01-01T15:00:00+00:00')); // UTC time
});

test('ListTupleChangesRequest handles partial parameters', function (): void {
    $request = new ListTupleChangesRequest(
        store: 'test-store',
        pageSize: 25,
        type: 'user',
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getUrl())->toContain('page_size=25');
    expect($context->getUrl())->toContain('type=user');
    expect($context->getUrl())->not->toContain('continuation_token');
    expect($context->getUrl())->not->toContain('start_time');
});

test('ListTupleChangesRequest converts startTime to UTC', function (): void {
    $startTime = new DateTimeImmutable('2024-01-01 00:00:00', new DateTimeZone('Asia/Tokyo'));

    $request = new ListTupleChangesRequest(
        store: 'test-store',
        startTime: $startTime,
    );

    $context = $request->getRequest($this->streamFactory);

    // Tokyo is UTC+9, so 2024-01-01 00:00:00 Tokyo = 2023-12-31 15:00:00 UTC
    expect($context->getUrl())->toContain('start_time=' . urlencode('2023-12-31T15:00:00+00:00'));
});

test('ListTupleChangesRequest handles empty type string', function (): void {
    $request = new ListTupleChangesRequest(
        store: 'test-store',
        type: '',
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getUrl())->toBe('/stores/test-store/changes?type=');
});

test('ListTupleChangesRequest handles special characters in parameters', function (): void {
    $request = new ListTupleChangesRequest(
        store: 'test-store',
        continuationToken: 'token/with+special=chars',
        type: 'type&with=special',
    );

    $context = $request->getRequest($this->streamFactory);

    expect($context->getUrl())->toContain('continuation_token=' . urlencode('token/with+special=chars'));
    expect($context->getUrl())->toContain('type=' . urlencode('type&with=special'));
});

it('throws when store is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new ListTupleChangesRequest(store: '');
});

it('throws when continuation token is empty', function (): void {
    $this->expectException(InvalidArgumentException::class);
    new ListTupleChangesRequest(store: 'test-store', continuationToken: '');
});
