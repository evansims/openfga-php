<?php

declare(strict_types=1);

namespace OpenFGA\Tests\Unit\Requests;

use OpenFGA\Exceptions\ClientException;
use OpenFGA\Network\RequestMethod;
use OpenFGA\Requests\ListStoresRequest;
use Psr\Http\Message\StreamFactoryInterface;

describe('ListStoresRequest', function (): void {
    test('can be instantiated without parameters', function (): void {
        $request = new ListStoresRequest;

        expect($request)->toBeInstanceOf(ListStoresRequest::class);
        expect($request->getContinuationToken())->toBeNull();
        expect($request->getPageSize())->toBeNull();
    });

    test('can be instantiated with all parameters', function (): void {
        $request = new ListStoresRequest(
            continuationToken: 'next-page-token',
            pageSize: 25,
        );

        expect($request->getContinuationToken())->toBe('next-page-token');
        expect($request->getPageSize())->toBe(25);
    });

    test('generates correct request context without pagination', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new ListStoresRequest;
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores');
        expect($context->getBody())->toBeNull();
        expect($context->getHeaders())->toBe([]);
    });
    test('generates correct request context with continuation token', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new ListStoresRequest(continuationToken: 'token123');
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores?continuation_token=token123');
    });

    test('generates correct request context with page size', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new ListStoresRequest(pageSize: 50);
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores?page_size=50');
    });

    test('generates correct request context with all pagination parameters', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new ListStoresRequest(
            continuationToken: 'next-token',
            pageSize: 100,
        );
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores?continuation_token=next-token&page_size=100');
    });

    test('handles special characters in continuation token', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $token = 'token with spaces & special=chars';
        $request = new ListStoresRequest(continuationToken: $token);
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores?continuation_token=' . urlencode($token));
    });

    test('filters out null values from query parameters', function (): void {
        $streamFactory = test()->createMock(StreamFactoryInterface::class);

        $request = new ListStoresRequest(
            continuationToken: null,
            pageSize: null,
        );
        $context = $request->getRequest($streamFactory);

        expect($context->getMethod())->toBe(RequestMethod::GET);
        expect($context->getUrl())->toBe('/stores');
    });

    test('throws when continuation token is empty', function (): void {
        new ListStoresRequest(continuationToken: '');
    })->throws(ClientException::class);
});
